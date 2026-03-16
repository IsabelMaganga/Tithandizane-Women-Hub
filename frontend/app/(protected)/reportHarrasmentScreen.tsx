import React, { useEffect, useState } from "react";
import { View, Text, Pressable, Alert } from "react-native";
import { LegendList } from "@legendapp/list";
import { FontAwesome5 } from "@expo/vector-icons";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { getReportHarrasment, submitHarassmentReport } from "@/services/api";
import CreateReportModal from "../../components/CreateReportModal";

const ReportsScreen = () => {
  const [showModal, setShowModal] = useState(false);
  const [reports, setReports] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);

  const fetchReports = async () => {
    try {
      const token = await AsyncStorage.getItem("token");
      const data = await getReportHarrasment(token!);
      setReports(data ?? []);
    } catch (e) { console.log(e); }
  };

  useEffect(() => { fetchReports(); }, []);

  const getStatus = (status: string) => {
    const s = status.toLowerCase();
    if (s === "approved") return { color: "text-green-600", bg: "bg-green-100", icon: "check-circle" };
    if (s === "pending") return { color: "text-amber-600", bg: "bg-amber-100", icon: "clock" };
    return { color: "text-blue-600", bg: "bg-blue-100", icon: "search" };
  };

  const handleCreate = async (payload: any) => {
    setLoading(true);
    try {
      const token = await AsyncStorage.getItem("token");
      await submitHarassmentReport({ token, ...payload });
      setShowModal(false);
      Alert.alert("Success", "Report filed safely.");
      fetchReports();
    } catch (e) {
      Alert.alert("Error", "Could not submit.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <View className="flex-1 bg-white p-4">
      <View className="bg-violet-100 p-5 rounded-2xl mb-4">
        <Text className="text-lg font-bold text-violet-900">Your Voice Matters</Text>
        <Text className="text-violet-700">Reporting helps keep the community safe.</Text>
      </View>

      <Pressable 
        onPress={() => setShowModal(true)}
        className="bg-violet-600 p-3 rounded-lg mb-6 shadow-sm active:bg-violet-700"
      >
        <Text className="text-white text-center font-bold">Create Report</Text>
      </Pressable>

      <LegendList
        data={reports}
        estimatedItemSize={120}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => {
          const status = getStatus(item.status);
          return (
            <View className="p-4 rounded-2xl border border-gray-100 mb-3 bg-white shadow-sm">
              <Text className="text-base font-bold text-gray-800">{item.incident_type}</Text>
              <Text className="text-gray-500 mt-1 mb-3" numberOfLines={2}>{item.description}</Text>
              <View className="flex-row justify-between items-center">
                <View className={`${status.bg} flex-row items-center px-3 py-1 rounded-full`}>
                  <FontAwesome5 name={status.icon as any} size={10} className={status.color} />
                  <Text className={`ml-2 text-xs font-semibold ${status.color}`}>
                    {item.status.toUpperCase()}
                  </Text>
                </View>
                <Text className="text-gray-400 text-[10px]">{item.created_at}</Text>
              </View>
            </View>
          );
        }}
      />

      <CreateReportModal
        visible={showModal}
        loading={loading}
        onClose={() => setShowModal(false)}
        onSubmit={handleCreate}
      />
    </View>
  );
};

export default ReportsScreen;