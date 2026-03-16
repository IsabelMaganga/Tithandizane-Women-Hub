import React, { useState } from "react";
import {
  View,
  Text,
  Pressable,
  Modal,
  TextInput,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  Alert,
} from "react-native";
import { Feather } from "@expo/vector-icons";
import DateTimePicker from "@react-native-community/datetimepicker";

type Props = {
  visible: boolean;
  onClose: () => void;
  onSubmit: (data: any) => void;
  loading: boolean;
};

const INCIDENT_TYPES = [
  "Verbal Harassment",
  "Physical Assault",
  "Cyberbullying",
  "Stalking",
  "Workplace Harassment",
  "Other",
];

const CreateReportModal = ({ visible, onClose, onSubmit, loading }: Props) => {
  const [incidentType, setIncidentType] = useState("");
  const [description, setDescription] = useState("");
  const [location, setLocation] = useState("");
  const [incidentDate, setIncidentDate] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [perpetratorInfo, setPerpetratorInfo] = useState("");
  const [isAnonymous, setIsAnonymous] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);

  const onDateChange = (event: any, selectedDate?: Date) => {
    setShowDatePicker(Platform.OS === "ios");
    if (selectedDate) setIncidentDate(selectedDate);
  };

  const handleFormSubmit = () => {
    if (!incidentType || description.length < 20) {
      Alert.alert("Missing Info", "Please select a type and provide 20+ chars.");
      return;
    }
    onSubmit({
      incident_type: incidentType,
      description,
      location,
      incident_date: incidentDate.toISOString().split("T")[0],
      perpetrator_info: perpetratorInfo,
      is_anonymous: isAnonymous,
    });
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : undefined}
        className="flex-1 justify-end bg-black/40"
      >
        <View className="bg-white p-5 rounded-t-3xl max-h-[90%]">
          <View className="flex-row justify-between items-center mb-4">
            <Text className="text-xl font-bold">New Report</Text>
            <Pressable onPress={onClose} className="p-1">
              <Feather name="x" size={24} color="black" />
            </Pressable>
          </View>

          <ScrollView showsVerticalScrollIndicator={false}>
            {/* Custom Dropdown for Incident Type */}
            <Text className="font-bold mb-1">Incident Type *</Text>
            <Pressable
              onPress={() => setShowDropdown(!showDropdown)}
              className="border border-gray-200 p-3 rounded-lg flex-row justify-between items-center mb-3"
            >
              <Text className={incidentType ? "text-black" : "text-gray-400"}>
                {incidentType || "Select type"}
              </Text>
              <Feather name={showDropdown ? "chevron-up" : "chevron-down"} size={18} color="gray" />
            </Pressable>

            {showDropdown && (
              <View className="border border-gray-100 rounded-lg mb-3 bg-gray-50 overflow-hidden">
                {INCIDENT_TYPES.map((type) => (
                  <Pressable
                    key={type}
                    onPress={() => { setIncidentType(type); setShowDropdown(false); }}
                    className="p-3 border-b border-gray-100 active:bg-violet-100"
                  >
                    <Text>{type}</Text>
                  </Pressable>
                ))}
              </View>
            )}

            <Text className="font-bold mb-1">Description *</Text>
            <TextInput
              className="border border-gray-200 p-3 rounded-lg h-24 mb-3"
              textAlignVertical="top"
              multiline
              placeholder="Tell us what happened..."
              value={description}
              onChangeText={setDescription}
            />

            <Text className="font-bold mb-1">Incident Date</Text>
            <Pressable
              onPress={() => setShowDatePicker(true)}
              className="border border-gray-200 p-3 rounded-lg mb-3"
            >
              <Text>{incidentDate.toDateString()}</Text>
            </Pressable>

            {showDatePicker && (
              <DateTimePicker
                value={incidentDate}
                mode="date"
                maximumDate={new Date()}
                onChange={onDateChange}
              />
            )}

            <Text className="font-bold mb-1">Location</Text>
            <TextInput
              className="border border-gray-200 p-3 rounded-lg mb-3"
              placeholder="Where did it happen?"
              value={location}
              onChangeText={setLocation}
            />

            <Text className="font-bold mb-1">Perpetrator Info</Text>
            <TextInput
              className="border border-gray-200 p-3 rounded-lg mb-4"
              placeholder="Name or physical traits"
              value={perpetratorInfo}
              onChangeText={setPerpetratorInfo}
            />

            <Pressable 
              onPress={() => setIsAnonymous(!isAnonymous)}
              className="flex-row items-center mb-6"
            >
              <View className={`w-5 h-5 border border-gray-300 rounded mr-2 items-center justify-center ${isAnonymous ? 'bg-violet-600 border-violet-600' : 'bg-white'}`}>
                {isAnonymous && <Feather name="check" size={14} color="white" />}
              </View>
              <Text className="text-gray-700">Submit Anonymously</Text>
            </Pressable>

            <Pressable
              onPress={handleFormSubmit}
              disabled={loading}
              className={`bg-violet-600 p-4 rounded-xl mb-10 ${loading ? 'opacity-50' : ''}`}
            >
              <Text className="text-white text-center font-bold text-lg">
                {loading ? "Submitting..." : "Submit Report"}
              </Text>
            </Pressable>
          </ScrollView>
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
};

export default CreateReportModal;