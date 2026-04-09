import React, { useState } from "react";
import {
  View,
  Text,
  TextInput,
  ScrollView,
  TouchableOpacity,
  Switch,
  ImageBackground,
  Image,
  Alert,
  KeyboardAvoidingView,
  Platform,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { Ionicons, MaterialIcons, FontAwesome5 } from "@expo/vector-icons";
import { useThemeToggle } from "../../hooks/useTheme";
import Animated, {
  FadeInDown,
  FadeInUp,
  ZoomIn,
} from "react-native-reanimated";

// ── Translations ──────────────────────────────────────────────────────────────
const translations = {
  en: {
    title: "Report Harassment",
    subtitle: "Your voice matters. You are safe here.",
    anonymous: "Stay Anonymous",
    anonymousDesc: "Your identity will not be revealed",
    name: "Your Name",
    namePlaceholder: "Enter your name",
    incidentType: "Type of Incident",
    types: [
      "Sexual Harassment",
      "Verbal Abuse",
      "Physical Abuse",
      "Cyberbullying",
      "Discrimination",
      "Other",
    ],
    description: "Describe the Incident",
    descPlaceholder:
      "Tell us what happened. Include as much detail as you feel comfortable sharing…",
    location: "Where did it happen?",
    locationPlaceholder: "e.g. School, Workplace, Online…",
    date: "When did it happen?",
    datePlaceholder: "e.g. Yesterday, 20 March 2026…",
    submit: "Submit Report",
    cancel: "Cancel",
    successTitle: "Report Submitted",
    successMsg:
      "Your report has been received safely. A trusted person will follow up confidentially.",
    required: "Please fill in the incident description before submitting.",
    privacyNote:
      "All reports are stored securely and reviewed only by authorized personnel.",
    selectType: "Select incident type",
  },
  ch: {
    title: "Landila Zoyipa",
    subtitle: "Mawu anu ndi ofunikira. Muli otetezedwa kuno.",
    anonymous: "Khalani Osadziwika",
    anonymousDesc: "Dzina lanu silidzaululidwa",
    name: "Dzina Lanu",
    namePlaceholder: "Lembani dzina lanu",
    incidentType: "Mtundu wa Vuto",
    types: [
      "Zokhudzana ndi Thupi",
      "Kutukwana",
      "Kumenya",
      "Zoyipa pa Intaneti",
      "Kusankhana",
      "Zina",
    ],
    description: "Fotokozani Zimene Zinachitika",
    descPlaceholder:
      "Tiuzeni zimene zinachitika. Lembani zambiri monga mwa kusangalala kwanu…",
    location: "Zinachitikira Kuti?",
    locationPlaceholder: "monga Sukulu, Ntchito, Intaneti…",
    date: "Zinachitika Liti?",
    datePlaceholder: "monga Dzulo, 20 March 2026…",
    submit: "Tumizani Lipoti",
    cancel: "Chokerani",
    successTitle: "Lipoti Yatumizidwa",
    successMsg:
      "Lipoti yanu yalandidwa bwinobwino. Munthu wokhulupirika adzakulumikizani mwachinsinsi.",
    required: "Chonde lembani za vuto musanagwirizane.",
    privacyNote:
      "Maulendo onse asungidwa bwinobwino ndipo amaonedwa ndi anthu opermitted okha.",
    selectType: "Sankhani mtundu wa vuto",
  },
};

// ── Component ─────────────────────────────────────────────────────────────────
export default function ReportHarrassmentScreen() {
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const isDark = colorScheme === "dark";

  // Detect language from i18n or default to 'en'
  // You can replace this with: const { i18n } = useTranslation(); const lang = i18n.language === "ch" ? "ch" : "en";
  const [lang, setLang] = useState<"en" | "ch">("en");
  const t = translations[lang];

  const [isAnonymous, setIsAnonymous] = useState(false);
  const [name, setName] = useState("");
  const [selectedType, setSelectedType] = useState<string | null>(null);
  const [description, setDescription] = useState("");
  const [location, setLocation] = useState("");
  const [date, setDate] = useState("");
  const [submitted, setSubmitted] = useState(false);

  const PURPLE = "#7C3AED";
  const ROSE = "#F43F5E";

  const handleSubmit = () => {
    if (!description.trim()) {
      Alert.alert("", t.required);
      return;
    }
    setSubmitted(true);
  };

  // ── Success Screen ──────────────────────────────────────────────────────────
  if (submitted) {
    return (
      <View className="flex-1 bg-gray-50 dark:bg-slate-900 items-center justify-center px-8">
        <StatusBar style={isDark ? "light" : "dark"} />
        <Animated.View entering={ZoomIn.duration(500)} className="items-center">
          <View
            style={{ backgroundColor: "#dcfce7" }}
            className="w-24 h-24 rounded-full items-center justify-center mb-6"
          >
            <Ionicons name="checkmark-circle" size={56} color="#16a34a" />
          </View>
          <Text className="text-slate-800 dark:text-white text-2xl font-bold text-center mb-3">
            {t.successTitle}
          </Text>
          <Text className="text-gray-500 dark:text-gray-400 text-center text-sm leading-6 mb-8">
            {t.successMsg}
          </Text>
          <TouchableOpacity
            onPress={() => router.back()}
            style={{ backgroundColor: PURPLE }}
            className="px-10 py-4 rounded-2xl"
          >
            <Text className="text-white font-bold text-base">
              {lang === "en" ? "Back to Home" : "Bwererani Kwanu"}
            </Text>
          </TouchableOpacity>
        </Animated.View>
      </View>
    );
  }

  // ── Main Form ───────────────────────────────────────────────────────────────
  return (
    <KeyboardAvoidingView
      className="flex-1"
      behavior={Platform.OS === "ios" ? "padding" : undefined}
    >
      <View className="flex-1 bg-gray-50 dark:bg-slate-900">
        <StatusBar style={isDark ? "light" : "dark"} />

        {/* ── Hero Header ── */}
        <ImageBackground
          source={require("../../assets/images/Ellipse 4.png")}
          className="w-full h-52"
          resizeMode="cover"
        >
          <Image
            source={require("../../assets/images/shape (1).png")}
            className="absolute top-0 left-0 w-32 h-32 opacity-60"
          />
          <SafeAreaView edges={["top"]}>
            <View className="flex-row items-center justify-between px-5 mt-2">
              {/* Back */}
              <TouchableOpacity
                onPress={() => router.back()}
                className="p-2 bg-white/20 rounded-full"
              >
                <Ionicons name="arrow-back" size={22} color="white" />
              </TouchableOpacity>

              {/* Language toggle */}
              <View className="flex-row bg-white/20 rounded-full overflow-hidden">
                {(["en", "ch"] as const).map((l) => (
                  <TouchableOpacity
                    key={l}
                    onPress={() => setLang(l)}
                    style={
                      lang === l ? { backgroundColor: "white" } : {}
                    }
                    className="px-4 py-1.5"
                  >
                    <Text
                      style={{ color: lang === l ? PURPLE : "white" }}
                      className="font-bold text-xs"
                    >
                      {l === "en" ? "ENG" : "CHI"}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>

            {/* Header text */}
            <Animated.View
              entering={FadeInDown.delay(100).duration(500)}
              className="px-6 mt-4"
            >
              <View className="flex-row items-center space-x-3">
                <View
                  style={{ backgroundColor: ROSE }}
                  className="w-10 h-10 rounded-xl items-center justify-center"
                >
                  <MaterialIcons name="report-problem" size={22} color="white" />
                </View>
                <View>
                  <Text className="text-white font-bold text-xl">{t.title}</Text>
                  <Text className="text-white/80 text-xs mt-0.5">{t.subtitle}</Text>
                </View>
              </View>
            </Animated.View>
          </SafeAreaView>
        </ImageBackground>

        {/* ── Form Card ── */}
        <ScrollView
          className="-mt-6"
          contentContainerStyle={{ paddingHorizontal: 16, paddingBottom: 40 }}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled"
        >
          <Animated.View
            entering={FadeInUp.delay(150).duration(500)}
            className="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm"
            style={{
              shadowColor: "#000",
              shadowOpacity: 0.06,
              shadowRadius: 12,
              elevation: 4,
            }}
          >

            {/* ── Anonymous Toggle ── */}
            <View
              style={{
                backgroundColor: isAnonymous
                  ? isDark ? "#1e1b4b" : "#ede9fe"
                  : isDark ? "#1e293b" : "#f8fafc",
                borderWidth: 1.5,
                borderColor: isAnonymous ? PURPLE : isDark ? "#334155" : "#e2e8f0",
              }}
              className="rounded-2xl p-4 mb-5 flex-row items-center justify-between"
            >
              <View className="flex-row items-center space-x-3 flex-1">
                <View
                  style={{
                    backgroundColor: isAnonymous ? PURPLE : isDark ? "#334155" : "#e2e8f0",
                  }}
                  className="w-10 h-10 rounded-xl items-center justify-center"
                >
                  <FontAwesome5
                    name={isAnonymous ? "user-secret" : "user"}
                    size={18}
                    color={isAnonymous ? "white" : isDark ? "#94a3b8" : "#64748b"}
                  />
                </View>
                <View className="flex-1">
                  <Text className="text-slate-800 dark:text-white font-bold text-sm">
                    {t.anonymous}
                  </Text>
                  <Text className="text-gray-500 dark:text-gray-400 text-xs mt-0.5">
                    {t.anonymousDesc}
                  </Text>
                </View>
              </View>
              <Switch
                value={isAnonymous}
                onValueChange={setIsAnonymous}
                trackColor={{ false: "#cbd5e1", true: PURPLE }}
                thumbColor="white"
              />
            </View>

            {/* ── Name Field (hidden when anonymous) ── */}
            {!isAnonymous && (
              <Animated.View entering={FadeInDown.duration(300)} className="mb-4">
                <Text className="text-slate-700 dark:text-slate-300 font-semibold text-sm mb-1.5">
                  {t.name}
                </Text>
                <View
                  className="flex-row items-center rounded-xl px-3"
                  style={{
                    backgroundColor: isDark ? "#0f172a" : "#f1f5f9",
                    borderWidth: 1,
                    borderColor: isDark ? "#334155" : "#e2e8f0",
                  }}
                >
                  <Ionicons
                    name="person-outline"
                    size={18}
                    color={isDark ? "#64748b" : "#94a3b8"}
                  />
                  <TextInput
                    value={name}
                    onChangeText={setName}
                    placeholder={t.namePlaceholder}
                    placeholderTextColor={isDark ? "#475569" : "#94a3b8"}
                    className="flex-1 text-slate-800 dark:text-white py-3 px-2 text-sm"
                  />
                </View>
              </Animated.View>
            )}

            {/* ── Incident Type ── */}
            <View className="mb-4">
              <Text className="text-slate-700 dark:text-slate-300 font-semibold text-sm mb-2">
                {t.incidentType}
              </Text>
              <View className="flex-row flex-wrap gap-2">
                {t.types.map((type) => (
                  <TouchableOpacity
                    key={type}
                    onPress={() =>
                      setSelectedType(selectedType === type ? null : type)
                    }
                    style={{
                      backgroundColor:
                        selectedType === type
                          ? ROSE
                          : isDark
                          ? "#1e293b"
                          : "#f1f5f9",
                      borderWidth: 1.5,
                      borderColor:
                        selectedType === type
                          ? ROSE
                          : isDark
                          ? "#334155"
                          : "#e2e8f0",
                    }}
                    className="rounded-full px-3 py-1.5"
                  >
                    <Text
                      style={{
                        color:
                          selectedType === type
                            ? "white"
                            : isDark
                            ? "#94a3b8"
                            : "#64748b",
                        fontSize: 12,
                        fontWeight: selectedType === type ? "700" : "500",
                      }}
                    >
                      {type}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>

            {/* ── Description ── */}
            <View className="mb-4">
              <Text className="text-slate-700 dark:text-slate-300 font-semibold text-sm mb-1.5">
                {t.description}{" "}
                <Text style={{ color: ROSE }}>*</Text>
              </Text>
              <TextInput
                value={description}
                onChangeText={setDescription}
                placeholder={t.descPlaceholder}
                placeholderTextColor={isDark ? "#475569" : "#94a3b8"}
                multiline
                numberOfLines={5}
                textAlignVertical="top"
                className="text-slate-800 dark:text-white text-sm p-3 rounded-xl"
                style={{
                  backgroundColor: isDark ? "#0f172a" : "#f1f5f9",
                  borderWidth: 1,
                  borderColor: isDark ? "#334155" : "#e2e8f0",
                  minHeight: 120,
                }}
              />
            </View>

            {/* ── Location ── */}
            <View className="mb-4">
              <Text className="text-slate-700 dark:text-slate-300 font-semibold text-sm mb-1.5">
                {t.location}
              </Text>
              <View
                className="flex-row items-center rounded-xl px-3"
                style={{
                  backgroundColor: isDark ? "#0f172a" : "#f1f5f9",
                  borderWidth: 1,
                  borderColor: isDark ? "#334155" : "#e2e8f0",
                }}
              >
                <Ionicons
                  name="location-outline"
                  size={18}
                  color={isDark ? "#64748b" : "#94a3b8"}
                />
                <TextInput
                  value={location}
                  onChangeText={setLocation}
                  placeholder={t.locationPlaceholder}
                  placeholderTextColor={isDark ? "#475569" : "#94a3b8"}
                  className="flex-1 text-slate-800 dark:text-white py-3 px-2 text-sm"
                />
              </View>
            </View>

            {/* ── Date ── */}
            <View className="mb-5">
              <Text className="text-slate-700 dark:text-slate-300 font-semibold text-sm mb-1.5">
                {t.date}
              </Text>
              <View
                className="flex-row items-center rounded-xl px-3"
                style={{
                  backgroundColor: isDark ? "#0f172a" : "#f1f5f9",
                  borderWidth: 1,
                  borderColor: isDark ? "#334155" : "#e2e8f0",
                }}
              >
                <Ionicons
                  name="calendar-outline"
                  size={18}
                  color={isDark ? "#64748b" : "#94a3b8"}
                />
                <TextInput
                  value={date}
                  onChangeText={setDate}
                  placeholder={t.datePlaceholder}
                  placeholderTextColor={isDark ? "#475569" : "#94a3b8"}
                  className="flex-1 text-slate-800 dark:text-white py-3 px-2 text-sm"
                />
              </View>
            </View>

            {/* ── Privacy Note ── */}
            <View
              className="flex-row items-start space-x-2 rounded-xl p-3 mb-5"
              style={{
                backgroundColor: isDark ? "#1e293b" : "#eff6ff",
                borderLeftWidth: 3,
                borderLeftColor: "#3b82f6",
              }}
            >
              <Ionicons name="shield-checkmark" size={16} color="#3b82f6" style={{ marginTop: 1 }} />
              <Text
                className="flex-1 text-xs leading-5"
                style={{ color: isDark ? "#93c5fd" : "#1d4ed8" }}
              >
                {t.privacyNote}
              </Text>
            </View>

            {/* ── Submit Button ── */}
            <TouchableOpacity
              onPress={handleSubmit}
              style={{ backgroundColor: ROSE }}
              className="rounded-2xl py-4 items-center mb-3 flex-row justify-center space-x-2"
            >
              <MaterialIcons name="send" size={18} color="white" />
              <Text className="text-white font-bold text-base ml-2">{t.submit}</Text>
            </TouchableOpacity>

            {/* ── Cancel ── */}
            <TouchableOpacity
              onPress={() => router.back()}
              className="rounded-2xl py-3.5 items-center"
              style={{
                backgroundColor: isDark ? "#1e293b" : "#f1f5f9",
              }}
            >
              <Text className="text-gray-500 dark:text-gray-400 font-semibold text-sm">
                {t.cancel}
              </Text>
            </TouchableOpacity>
          </Animated.View>
        </ScrollView>
      </View>
    </KeyboardAvoidingView>
  );
}