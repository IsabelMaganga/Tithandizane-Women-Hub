import { View, Text, ScrollView, Pressable, LayoutAnimation, Platform, UIManager } from "react-native";
import React, { useState } from "react";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";

// Enable LayoutAnimation on Android
if (Platform.OS === "android") {
  UIManager.setLayoutAnimationEnabledExperimental?.(true);
}

const FAQ_DATA = [
  {
    question: "What is the purpose of this platform?",
    answer:
      "The platform is designed to empower women and girls by providing access to knowledge, safety resources, and community support."
  },
  {
    question: "Who can use this app?",
    answer:
      "It is open to all women and girls who want access to empowerment resources, support services, and educational content."
  },
  {
    question: "Is my personal information safe?",
    answer:
      "Yes, user privacy is a top priority. Personal data is protected and not shared without consent."
  },
  {
    question: "Does the app work offline?",
    answer:
      "Some content may be available offline if previously loaded, but most features require an internet connection."
  },
  {
    question: "What kind of support is available?",
    answer:
      "Users can access educational resources, safety guidance, and community-based support materials."
  },
  {
    question: "Can I report unsafe or harmful content?",
    answer:
      "Yes, users are encouraged to report any harmful content so it can be reviewed and removed if necessary."
  },
  {
    question: "Does the platform provide emergency help?",
    answer:
      "The app provides guidance and information for emergencies but users should contact local emergency services when in immediate danger."
  },
  {
    question: "How often is content updated?",
    answer:
      "Content is regularly updated to ensure users receive relevant and accurate information."
  },
  {
    question: "Can I access educational materials?",
    answer:
      "Yes, the platform includes educational resources focused on health, rights, safety, and personal development."
  },
  {
    question: "Is this app free to use?",
    answer:
      "Yes, all core features and resources are freely accessible."
  },
  {
    question: "Does the app require registration to use?",
    answer:
      "Some features may require registration, but general information can be accessed freely."
  },
  {
    question: "Can I use the app in rural areas?",
    answer:
      "Yes, but performance may depend on internet availability in your area."
  },
  {
    question: "What should I do if I forget my login details?",
    answer:
      "You can use the password recovery option to regain access to your account."
  },
  {
    question: "Does the app provide health-related information?",
    answer:
      "Yes, it includes verified information on women’s health and wellbeing."
  },
  {
    question: "Can I interact with other users?",
    answer:
      "Some features may allow community interaction depending on the app version and permissions."
  },
  {
    question: "How is harmful behavior handled?",
    answer:
      "The platform has moderation systems to detect and manage abusive or harmful behavior."
  },
  {
    question: "Can I suggest improvements to the app?",
    answer:
      "Yes, user feedback is encouraged to improve features and services."
  },
  {
    question: "Does the app support multiple languages?",
    answer:
      "Language support may be expanded over time to improve accessibility."
  },
  {
    question: "What topics are covered in the app?",
    answer:
      "Topics include empowerment, health, safety, rights, education, and personal development."
  },
  {
    question: "What should I do if I experience a problem in the app?",
    answer:
      "You should report the issue through the support or feedback section so it can be resolved."
  }
];

export default function FAQScreen() {
  const [openIndex, setOpenIndex] = useState(null);

  const toggleItem = (index) => {
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setOpenIndex(openIndex === index ? null : index);
  };

  return (
    <SafeAreaView className="flex-1 bg-white px-4">
      <Text className="text-2xl font-bold mt-4 mb-6 text-center">
        Frequently Asked Questions
      </Text>

      <ScrollView showsVerticalScrollIndicator={false}>
        {FAQ_DATA.map((item, index) => {
          const isOpen = openIndex === index;

          return (
            <View
              key={index}
              className="mb-3 border border-gray-200 rounded-xl overflow-hidden"
            >
              {/* Question */}
              <Pressable
                onPress={() => toggleItem(index)}
                className="flex-row justify-between items-center p-4 bg-gray-100"
              >
                <Text className="text-base font-semibold flex-1 pr-2">
                  {item.question}
                </Text>

                <Ionicons
                  name={isOpen ? "chevron-up" : "chevron-down"}
                  size={20}
                  color="black"
                />
              </Pressable>

              {/* Answer */}
              {isOpen && (
                <View className="p-4 bg-white">
                  <Text className="text-gray-700 leading-5">
                    {item.answer}
                  </Text>
                </View>
              )}
            </View>
          );
        })}
      </ScrollView>
    </SafeAreaView>
  );
}