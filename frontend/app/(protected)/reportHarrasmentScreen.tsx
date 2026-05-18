// app/(protected)/report-harassment.tsx
import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TextInput,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  Switch,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { useLanguage } from '../../context/LanguageContext';
import { submitHarassmentReport, submitAnonymousReport } from '../../services/api'; // Use your existing functions
import { Ionicons } from '@expo/vector-icons';

// Types for form data
interface ReportFormData {
  incident_type: string;
  incident_title: string;
  incident_description: string;
  incident_date: string;
  incident_location: string;
  perpetrator_info: string;
  is_anonymous: boolean;
  victim_name: string;
  victim_email: string;
  victim_phone: string;
}

// Incident types with bilingual labels
const INCIDENT_TYPES = {
  physical: { en: 'Physical Harassment', ny: 'Kuzunzidwa Kwakuthupi' },
  verbal: { en: 'Verbal Harassment', ny: 'Kuzunzidwa Pakamwa' },
  sexual: { en: 'Sexual Harassment', ny: 'Kuzunzidwa Kogonana' },
  cyber: { en: 'Cyber Harassment', ny: 'Kuzunzidwa Pa Intaneti' },
  other: { en: 'Other', ny: 'Zina' },
};

export default function HarassmentReportScreen() {
  const router = useRouter();
  const { language, t } = useLanguage();
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState<ReportFormData>({
    incident_type: 'physical',
    incident_title: '',
    incident_description: '',
    incident_date: new Date().toISOString().split('T')[0],
    incident_location: '',
    perpetrator_info: '',
    is_anonymous: true,
    victim_name: '',
    victim_email: '',
    victim_phone: '',
  });

  const [errors, setErrors] = useState<Partial<Record<keyof ReportFormData, string>>>({});

  // Translation function
  const translate = (en: string, ny: string) => (language === 'en' ? en : ny);

  // Validate form
  const validateForm = (): boolean => {
    const newErrors: Partial<Record<keyof ReportFormData, string>> = {};

    if (!formData.incident_title.trim()) {
      newErrors.incident_title = translate('Incident title is required', 'Mutu wa chochitika ukufunika');
    }
    if (!formData.incident_description.trim()) {
      newErrors.incident_description = translate('Description is required', 'Kufotokozera kukufunika');
    }
    if (!formData.incident_date) {
      newErrors.incident_date = translate('Incident date is required', 'Tsiku lochitikira likufunika');
    }
    if (!formData.incident_location.trim()) {
      newErrors.incident_location = translate('Location is required', 'Malo akufunika');
    }

    // Validate non-anonymous fields
    if (!formData.is_anonymous) {
      if (!formData.victim_name.trim()) {
        newErrors.victim_name = translate('Name is required', 'Dzina likufunika');
      }
      if (!formData.victim_email.trim()) {
        newErrors.victim_email = translate('Email is required', 'Imelo ikufunika');
      } else if (!/\S+@\S+\.\S+/.test(formData.victim_email)) {
        newErrors.victim_email = translate('Valid email is required', 'Imelo yovomerezeka ikufunika');
      }
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Handle form submission
  const handleSubmit = async () => {
    if (!validateForm()) return;

    setLoading(true);
    try {
      let response;
      
      if (formData.is_anonymous) {
        // Use anonymous report function
        response = await submitAnonymousReport({
          title: formData.incident_title.trim(),
          description: formData.incident_description.trim(),
          location: formData.incident_location.trim(),
        });
      } else {
        // Use regular report function with user info
        response = await submitHarassmentReport({
          incident_type: formData.incident_type,
          description: formData.incident_description.trim(),
          incident_location: formData.incident_location.trim(),
          incident_date: formData.incident_date,
          perpetrator_info: formData.perpetrator_info.trim() || undefined,
          is_anonymous: formData.is_anonymous ? 'yes' : 'no',
        });
      }

      Alert.alert(
        translate('Report Submitted', 'Lipoti Latumizidwa'),
        translate(
          'Your report has been submitted successfully. An administrator will review it and respond shortly.',
          'Lipoti lanu latumizidwa bwino. Woyang\'anira adzaliwunika ndikuyankha posachedwa.'
        ),
        [
          {
            text: translate('OK', 'Chabwino'),
            onPress: () => router.back(),
          },
        ]
      );
    } catch (error: any) {
      console.error('Submission error:', error);
      Alert.alert(
        translate('Error', 'Vuto'),
        error.response?.data?.message || 
        error.message || 
        translate('Failed to submit report. Please try again.', 'Kutumiza lipoti kwalephera. Chonde yesaninso.')
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-gray-50 dark:bg-gray-900">
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        className="flex-1"
      >
        {/* Header */}
        <View className="bg-white dark:bg-gray-800 px-4 py-4 border-b border-gray-200 dark:border-gray-700">
          <View className="flex-row items-center">
            <TouchableOpacity onPress={() => router.back()} className="mr-4 p-1">
              <Ionicons name="arrow-back" size={24} color="#4B5563" />
            </TouchableOpacity>
            <View className="flex-1">
              <Text className="text-xl font-bold text-gray-900 dark:text-white">
                {translate('Report Harassment', 'Lipoti Zachipongwe')}
              </Text>
              <Text className="text-sm text-gray-500 dark:text-gray-400">
                {translate(
                  'Your report will be handled confidentially',
                  'Lipoti lanu lidzasungidwa mwachinsinsi'
                )}
              </Text>
            </View>
          </View>
        </View>

        <ScrollView
          className="flex-1 px-4 pt-4"
          showsVerticalScrollIndicator={false}
        >
          {/* Anonymous Switch Section */}
          <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4 shadow-sm">
            <View className="flex-row justify-between items-center">
              <View className="flex-1">
                <Text className="text-base font-semibold text-gray-900 dark:text-white">
                  {translate('Submit Anonymously', 'Tumiza Mosadziwika')}
                </Text>
                <Text className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  {translate(
                    'Your personal information will not be shared',
                    'Zambiri zanu sizidzaululidwa'
                  )}
                </Text>
              </View>
              <Switch
                value={formData.is_anonymous}
                onValueChange={(value) =>
                  setFormData((prev) => ({ ...prev, is_anonymous: value }))
                }
                trackColor={{ false: '#D1D5DB', true: '#3B82F6' }}
                thumbColor="#FFFFFF"
              />
            </View>
          </View>

          {/* Incident Type */}
          <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4">
            <Text className="text-base font-semibold text-gray-900 dark:text-white mb-3">
              {translate('Type of Harassment', 'Mtundu wa Nkhanza')} <Text className="text-red-500">*</Text>
            </Text>
            <View className="flex-row flex-wrap gap-2">
              {Object.entries(INCIDENT_TYPES).map(([key, labels]) => (
                <TouchableOpacity
                  key={key}
                  onPress={() => setFormData((prev) => ({ ...prev, incident_type: key }))}
                  className={`px-4 py-2 rounded-full ${
                    formData.incident_type === key
                      ? 'bg-blue-600'
                      : 'bg-gray-100 dark:bg-gray-700'
                  }`}
                >
                  <Text
                    className={`${
                      formData.incident_type === key
                        ? 'text-white'
                        : 'text-gray-700 dark:text-gray-300'
                    }`}
                  >
                    {translate(labels.en, labels.ny)}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {/* Incident Title */}
          <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4">
            <Text className="text-base font-semibold text-gray-900 dark:text-white mb-2">
              {translate('Incident Title', 'Mutu wa Chochitika')} <Text className="text-red-500">*</Text>
            </Text>
            <TextInput
              className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
              placeholder={translate('e.g., Harassment at school', 'Mwachitsanzo, Kuponderezedwa kusukulu')}
              placeholderTextColor="#9CA3AF"
              value={formData.incident_title}
              onChangeText={(text) =>
                setFormData((prev) => ({ ...prev, incident_title: text }))
              }
            />
            {errors.incident_title && (
              <Text className="text-red-500 text-sm mt-1">{errors.incident_title}</Text>
            )}
          </View>

          {/* Incident Description */}
          <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4">
            <Text className="text-base font-semibold text-gray-900 dark:text-white mb-2">
              {translate('Description of Incident', 'Kufotokozera Chochitika')}{' '}
              <Text className="text-red-500">*</Text>
            </Text>
            <TextInput
              className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 min-h-[120px]"
              placeholder={translate(
                'Please provide as much detail as possible...',
                'Chonde fotokozani mwatsatanetsatane...'
              )}
              placeholderTextColor="#9CA3AF"
              multiline
              numberOfLines={6}
              textAlignVertical="top"
              value={formData.incident_description}
              onChangeText={(text) =>
                setFormData((prev) => ({ ...prev, incident_description: text }))
              }
            />
            {errors.incident_description && (
              <Text className="text-red-500 text-sm mt-1">{errors.incident_description}</Text>
            )}
          </View>

          {/* Incident Date & Location Row */}
          <View className="flex-row gap-4 mb-4">
            <View className="flex-1 bg-white dark:bg-gray-800 rounded-xl p-4">
              <Text className="text-base font-semibold text-gray-900 dark:text-white mb-2">
                {translate('Date of Incident', 'Tsiku lochitikira')} <Text className="text-red-500">*</Text>
              </Text>
              <TextInput
                className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
                placeholder="YYYY-MM-DD"
                placeholderTextColor="#9CA3AF"
                value={formData.incident_date}
                onChangeText={(text) =>
                  setFormData((prev) => ({ ...prev, incident_date: text }))
                }
              />
              {errors.incident_date && (
                <Text className="text-red-500 text-sm mt-1">{errors.incident_date}</Text>
              )}
            </View>

            <View className="flex-1 bg-white dark:bg-gray-800 rounded-xl p-4">
              <Text className="text-base font-semibold text-gray-900 dark:text-white mb-2">
                {translate('Location', 'Malo')} <Text className="text-red-500">*</Text>
              </Text>
              <TextInput
                className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
                placeholder={translate('e.g., School, Home, Work', 'Mwachitsanzo, Sukulu, Kunyumba, Kuntchito')}
                placeholderTextColor="#9CA3AF"
                value={formData.incident_location}
                onChangeText={(text) =>
                  setFormData((prev) => ({ ...prev, incident_location: text }))
                }
              />
              {errors.incident_location && (
                <Text className="text-red-500 text-sm mt-1">{errors.incident_location}</Text>
              )}
            </View>
          </View>

          {/* Perpetrator Information */}
          <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4">
            <Text className="text-base font-semibold text-gray-900 dark:text-white mb-2">
              {translate('Perpetrator Information', 'Zambiri za Wokuzunza')}
              <Text className="text-sm font-normal text-gray-500">
                {' '}
                ({translate('Optional', 'Mwasankha')})
              </Text>
            </Text>
            <TextInput
              className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
              placeholder={translate(
                'Any information about the perpetrator (name, description, etc.)',
                'Zambiri zilizonse za wokuzunzayo (dzina, mawonekedwe, ndi zina)'
              )}
              placeholderTextColor="#9CA3AF"
              multiline
              numberOfLines={3}
              textAlignVertical="top"
              value={formData.perpetrator_info}
              onChangeText={(text) =>
                setFormData((prev) => ({ ...prev, perpetrator_info: text }))
              }
            />
          </View>

          {/* Non-Anonymous Information Section */}
          {!formData.is_anonymous && (
            <View className="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4">
              <Text className="text-base font-semibold text-gray-900 dark:text-white mb-3">
                {translate('Your Contact Information', 'Zambiri Yanu Yolumikizana')}
              </Text>
              <View className="mb-3">
                <Text className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {translate('Full Name', 'Dzina Lathunthu')} <Text className="text-red-500">*</Text>
                </Text>
                <TextInput
                  className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
                  placeholder={translate('Enter your full name', 'Lowetsani dzina lanu lathunthu')}
                  placeholderTextColor="#9CA3AF"
                  value={formData.victim_name}
                  onChangeText={(text) =>
                    setFormData((prev) => ({ ...prev, victim_name: text }))
                  }
                />
                {errors.victim_name && (
                  <Text className="text-red-500 text-sm mt-1">{errors.victim_name}</Text>
                )}
              </View>

              <View className="mb-3">
                <Text className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {translate('Email Address', 'Imelo')} <Text className="text-red-500">*</Text>
                </Text>
                <TextInput
                  className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
                  placeholder="example@email.com"
                  placeholderTextColor="#9CA3AF"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  value={formData.victim_email}
                  onChangeText={(text) =>
                    setFormData((prev) => ({ ...prev, victim_email: text }))
                  }
                />
                {errors.victim_email && (
                  <Text className="text-red-500 text-sm mt-1">{errors.victim_email}</Text>
                )}
              </View>

              <View>
                <Text className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {translate('Phone Number', 'Nambala ya Foni')}
                  <Text className="text-sm font-normal text-gray-500">
                    {' '}({translate('Optional', 'Mwasankha')})
                  </Text>
                </Text>
                <TextInput
                  className="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700"
                  placeholder={translate('e.g., +265 999 123 456', 'Mwachitsanzo, +265 999 123 456')}
                  placeholderTextColor="#9CA3AF"
                  keyboardType="phone-pad"
                  value={formData.victim_phone}
                  onChangeText={(text) =>
                    setFormData((prev) => ({ ...prev, victim_phone: text }))
                  }
                />
              </View>
            </View>
          )}

          {/* Privacy Notice */}
          <View className="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-6">
            <View className="flex-row items-start">
              <Ionicons name="shield-checkmark" size={20} color="#3B82F6" />
              <Text className="text-sm text-blue-700 dark:text-blue-300 ml-2 flex-1">
                {translate(
                  'All reports are encrypted and accessible only by authorized administrators. Your safety and confidentiality are our priority.',
                  'Mauthenga onse ndi achinsinsi ndipo amawonedwa ndi oyang\'anira okhawo. Chitetezo chanu ndi chinsinsi ndizofunika kwambiri.'
                )}
              </Text>
            </View>
          </View>

          {/* Submit Button */}
          <TouchableOpacity
            onPress={handleSubmit}
            disabled={loading}
            className={`bg-blue-600 rounded-xl py-4 mb-8 ${
              loading ? 'opacity-70' : ''
            }`}
          >
            {loading ? (
              <ActivityIndicator color="#FFFFFF" />
            ) : (
              <Text className="text-white text-center text-lg font-semibold">
                {translate('Submit Report', 'Tumizani Lipoti')}
              </Text>
            )}
          </TouchableOpacity>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}