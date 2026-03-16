import { View, Image } from "react-native";
import React from "react";
import { useAuth } from "../hooks/useAuth";
import { MaterialCommunityIcons } from "@expo/vector-icons";

const Profile = () => {
    const user = useAuth();

return (
    <View className="items-center justify-center mr-2">
        {user?.image ? (
        <Image
            source={{ uri: user.image }}
            className="w-12 h-12 rounded-full"
        />
        ) : (
        <View className="w-10 h-10 rounded-full bg-gray-200 items-center justify-center">
          <MaterialCommunityIcons name="account" size={28} color="#777" />
        </View>
      )}
    </View>
  );
};

export default Profile;