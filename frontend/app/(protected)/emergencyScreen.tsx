import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  Modal,
  Linking,
  Pressable,
  ActivityIndicator,
} from "react-native";
import { getEmergencyContacts } from "@/services/api";
import { LegendList } from "@legendapp/list";
import { Ionicons, Feather } from "@expo/vector-icons";
import LottieView from "lottie-react-native";
import { SafeAreaView } from "react-native-safe-area-context";

type Helpline = {
  id: number;
  name: string;
  phone: string;
};

export default function Helplines() {
  const [helplines, setHelplines] = useState<Helpline[]>([]);
  const [selectedContact, setSelectedContact] = useState<Helpline | null>(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    fetchHelplines();
  }, []);

  const fetchHelplines = async () => {
    try {
      setIsLoading(true);
      const data = await getEmergencyContacts();
      setHelplines(data ?? []);
    } catch (error) {
      console.log("Error fetching contacts:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const confirmCall = (contact: Helpline) => {
    setSelectedContact(contact);
    setModalVisible(true);
  };

  const makeCall = () => {
    if (selectedContact?.phone) {
      Linking.openURL(`tel:${selectedContact.phone}`);
    }
    setModalVisible(false);
  };

  if (isLoading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width:200, height:200 }}
        />
        <Text className="text-gray-500 font-medium mt-4">Loading Helplines...</Text>
      </View>
    );
  }

  return (
    <View className="flex-1 bg-slate-50">
      {/* Header Section */}
      <View className="bg-rose-500 pt-12 pb-8 px-6 rounded-b-[40px] shadow-lg">
        <Text className="text-white text-3xl font-bold">Emergency</Text>
        <Text className="text-rose-100 text-lg">Help is just a call away</Text>
      </View>

      <View className="flex-1 px-5 -mt-6">
        <LegendList
          data={helplines}
          estimatedItemSize={90}
          keyExtractor={(item) => item.id.toString()}
          ListEmptyComponent={
            <View className="items-center mt-20">
              <Feather name="phone-off" size={48} color="#cbd5e1" />
              <Text className="text-slate-400 mt-4 text-center">No helpline contacts available at the moment.</Text>
            </View>
          }
          renderItem={({ item }) => (
            <Pressable 
              onPress={() => confirmCall(item)}
              className="bg-white p-5 mb-4 rounded-3xl flex-row items-center justify-between shadow-sm border border-slate-100 active:bg-slate-50"
            >
              <View className="flex-row items-center flex-1">
                <View className="bg-rose-100 p-3 rounded-2xl mr-4">
                  <Feather name="shield" size={24} color="#e11d48" />
                </View>
                <View className="flex-1">
                  <Text className="text-slate-800 text-base font-bold" numberOfLines={1}>
                    {item.name}
                  </Text>
                  <Text className="text-slate-500 font-medium mt-1">
                    {item.phone}
                  </Text>
                </View>
              </View>

              <View className="bg-green-500 w-12 h-12 rounded-full items-center justify-center shadow-md">
                <Ionicons name="call" size={22} color="white" />
              </View>
            </Pressable>
          )}
        />
      </View>

      {/*Call Confirmation Modal */}
      <Modal visible={modalVisible} transparent animationType="slide">
        <View className="flex-1 justify-end bg-black/50">
          <View className="bg-white p-8 rounded-t-[40px] items-center">

            <View className="w-12 h-1.5 bg-slate-200 rounded-full mb-6" />
            
            <View className="bg-rose-100 p-6 rounded-full mb-4">
              <Ionicons name="alert-circle" size={40} color="#e11d48" />
            </View>

            <Text className="text-slate-900 text-xl font-bold text-center">
              Call {selectedContact?.name}?
            </Text>
            <Text className="text-slate-500 text-center mt-2 mb-8 px-4 text-base">
              You are about to dial {selectedContact?.phone}. Standard call rates may apply.
            </Text>

            <View className="flex-row w-full space-x-4 justify-between">
              <Pressable 
                onPress={() => setModalVisible(false)}
                className="flex-1 bg-slate-100 py-4 rounded-2xl active:bg-slate-200"
              >
                <Text className="text-slate-600 text-center font-bold text-lg">Cancel</Text>
              </Pressable>

              <Pressable 
                onPress={makeCall}
                className="flex-1 bg-green-500 py-4 rounded-2xl shadow-lg active:bg-green-600"
              >
                <Text className="text-white text-center font-bold text-lg font-bold">Call Now</Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}