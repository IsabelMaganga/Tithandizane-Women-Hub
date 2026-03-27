
import React, { useState } from "react";
import {
  View,
  Text,
  Pressable,
  Modal,
  TouchableOpacity,
  FlatList,
  Image
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useAuth } from "../../context/AuthContext";
import {
  FontAwesome,
  MaterialIcons,
  MaterialCommunityIcons,
  AntDesign
} from "@expo/vector-icons";
import i18n from "i18next";
import { useTranslation } from "react-i18next";


const Setting = () => {
  const { t } = useTranslation("settings");
  const { user, logout } = useAuth();

  const [modalVisible, setModalVisible] = useState(false);
  const [selectedLanguage, setSelectedLanguage] = useState(i18n.language || "en");

  const languages = [
    { code: "en", label: "English" },
    { code: "ch", label: "Chichewa" },
    { code: "tu", label: "Tumbuka" }
  ];

  const changeLanguage = (lang: string) => {
    i18n.changeLanguage(lang);
    setSelectedLanguage(lang);
    setModalVisible(false);
  };

  const currentLanguageLabel =
    languages.find((l) => l.code === selectedLanguage)?.label || "";

  return (
    <SafeAreaView style={{ flex: 1, padding: 15 }}>

      {/* Profile */}
      <View
        style={{
          flexDirection: "row",
          alignItems: "center",
          justifyContent: "space-between",
          backgroundColor: "#E5E7EB",
          padding: 15,
          borderRadius: 12
        }}
      >
        
        <View style={{ flexDirection: "row", alignItems: "center", gap: 12 }}>
          {user?.profile_url ? (
            <Image
              source={{ uri: user.profile_url }}
              style={{ width: 50, height: 50, borderRadius: 25 }}
            />
          ) : (
            <MaterialCommunityIcons
              name="face-woman-profile"
              size={45}
              color="black"
            />
          )}

          <View>
            <Text style={{ fontSize: 18, fontWeight: "bold" }}>
              {user?.name}
            </Text>
            <Text style={{ color: "gray" }}>{user?.email}</Text>
          </View>
        </View>

        <MaterialIcons name="arrow-forward-ios" size={18} color="gray" />
      </View>

      {/* Language */}
      <Pressable
        onPress={() => setModalVisible(true)}
        style={{
          flexDirection: "row",
          alignItems: "center",
          justifyContent: "space-between",
          backgroundColor: "#E5E7EB",
          padding: 15,
          borderRadius: 12,
          marginTop: 18
        }}
      >
        <View style={{ flexDirection: "row", alignItems: "center", gap: 12 }}>
          <FontAwesome name="language" size={22} />
          <Text style={{ fontWeight: "bold", fontSize: 16 }}>
            {t("language")}
          </Text>
        </View>

        <View style={{ flexDirection: "row", alignItems: "center", gap: 6 }}>
          <Text style={{ color: "gray" }}>{currentLanguageLabel}</Text>
          <MaterialIcons name="arrow-forward-ios" size={18} color="gray" />
        </View>
      </Pressable>

      {/* 2FA */}
      <Pressable
        style={{
          flexDirection: "row",
          alignItems: "center",
          justifyContent: "space-between",
          backgroundColor: "#E5E7EB",
          padding: 15,
          borderRadius: 12,
          marginTop: 15
        }}
      >
        <View style={{ flexDirection: "row", alignItems: "center", gap: 12 }}>
          <MaterialCommunityIcons
            name="two-factor-authentication"
            size={24}
          />
          <Text style={{ fontWeight: "bold", fontSize: 16 }}>
            {t("2fa")}
          </Text>
        </View>

        <MaterialIcons name="arrow-forward-ios" size={18} color="gray" />
      </Pressable>

      {/* Logout */}
      <Pressable
        onPress={logout}
        style={{
          flexDirection: "row",
          alignItems: "center",
          justifyContent: "center",
          backgroundColor: "#7C3AED",
          padding: 14,
          borderRadius: 10,
          marginTop: 25
        }}
      >
        <AntDesign name="logout" size={20} color="white" />
        <Text
          style={{
            color: "white",
            fontWeight: "bold",
            marginLeft: 8
          }}
        >
          Logout
        </Text>
      </Pressable>

      {/* Footer */}
      <View
        style={{
          alignItems: "center",
          marginTop: 40
        }}
      >
        <Image
          source={require("../../assets/images/Ellipse 3.png")}
          style={{ width: 50, height: 50, borderRadius: 25 }}
        />
        <Text style={{ marginTop: 6 }}>Developed By</Text>
      </View>

      {/* Language Modal */}
      <Modal
        animationType="slide"
        transparent
        visible={modalVisible}
      >
        <View
          style={{
            flex: 1,
            backgroundColor: "rgba(0,0,0,0.5)",
            justifyContent: "center",
            alignItems: "center"
          }}
        >
          <View
            style={{
              width: "80%",
              backgroundColor: "white",
              borderRadius: 12,
              padding: 20
            }}
          >
            <Text
              style={{
                fontSize: 18,
                fontWeight: "bold",
                marginBottom: 15
              }}
            >
              {t("select_language")}
            </Text>

            <FlatList
              data={languages}
              keyExtractor={(item) => item.code}
              renderItem={({ item }) => (
                <TouchableOpacity
                  onPress={() => changeLanguage(item.code)}
                  style={{
                    paddingVertical: 12,
                    borderBottomWidth: 1,
                    borderBottomColor: "#eee"
                  }}
                >
                  <Text style={{ fontSize: 16 }}>{item.label}</Text>
                </TouchableOpacity>
              )}
            />

            <Pressable
              onPress={() => setModalVisible(false)}
              style={{
                marginTop: 15,
                padding: 10,
                backgroundColor: "#eee",
                alignItems: "center",
                borderRadius: 8
              }}
            >
              <Text>Cancel</Text>
            </Pressable>
          </View>
        </View>
      </Modal>

    </SafeAreaView>
  );
};

export default Setting;

