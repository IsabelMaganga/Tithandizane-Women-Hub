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
  Modal,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { useLanguage } from '../../context/LanguageContext';
import { submitHarassmentReport, submitAnonymousReport } from '../../services/api';
import { Ionicons, MaterialCommunityIcons, FontAwesome5 } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';

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

// Incident types with bilingual labels and icons
const INCIDENT_TYPES = {
  physical: { 
    en: 'Physical Harassment', 
    ny: 'Kuzunzidwa Kwakuthupi',
    icon: 'fist',
    iconSet: 'material',
    color: '#EF4444'
  },
  verbal: { 
    en: 'Verbal Harassment', 
    ny: 'Kuzunzidwa Pakamwa',
    icon: 'chatbubbles',
    iconSet: 'ion',
    color: '#F59E0B'
  },
  sexual: { 
    en: 'Sexual Harassment', 
    ny: 'Kuzunzidwa Kogonana',
    icon: 'heart-dislike',
    iconSet: 'ion',
    color: '#EC4899'
  },
  cyber: { 
    en: 'Cyber Harassment', 
    ny: 'Kuzunzidwa Pa Intaneti',
    icon: 'laptop',
    iconSet: 'ion',
    color: '#06B6D4'
  },
  other: { 
    en: 'Other', 
    ny: 'Zina',
    icon: 'help-circle',
    iconSet: 'ion',
    color: '#6B7280'
  },
};

export default function HarassmentReportScreen() {
  const router = useRouter();
  const { language, t } = useLanguage();
  const [loading, setLoading] = useState(false);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [selectedDate, setSelectedDate] = useState(new Date());
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

  // Format date for display
  const formatDate = (date: Date) => {
    return date.toISOString().split('T')[0];
  };

  // Handle date change
  const onDateChange = (event: any, date?: Date) => {
    if (Platform.OS === 'android') {
      setShowDatePicker(false);
    }
    if (date) {
      setSelectedDate(date);
      setFormData(prev => ({ ...prev, incident_date: formatDate(date) }));
    }
  };

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

  // Handle form submission - FIXED with incident_title included
  const handleSubmit = async () => {
    if (!validateForm()) return;

    setLoading(true);
    try {
      let response;
      
      if (formData.is_anonymous) {
        // For anonymous reports - using submitAnonymousReport
        response = await submitAnonymousReport({
          title: formData.incident_title.trim(),
          description: formData.incident_description.trim(),
          location: formData.incident_location.trim(),
        });
        console.log('Anonymous report submitted:', response);
      } else {
        // For non-anonymous reports - include incident_title
        response = await submitHarassmentReport({
          incident_type: formData.incident_type,
          incident_title: formData.incident_title.trim(),
          description: formData.incident_description.trim(),
          incident_location: formData.incident_location.trim(),
          incident_date: formData.incident_date,
          perpetrator_info: formData.perpetrator_info.trim() || undefined,
          is_anonymous: 'no',
        });
        console.log('Non-anonymous report submitted:', response);
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
      
      let errorMessage = translate('Failed to submit report. Please try again.', 'Kutumiza lipoti kwalephera. Chonde yesaninso.');
      
      if (error.response?.data?.errors) {
        const validationErrors = Object.values(error.response.data.errors).flat();
        errorMessage = validationErrors.join('\n');
      } else if (error.response?.data?.message) {
        errorMessage = error.response.data.message;
      } else if (error.message && !error.message.includes('Unable to connect')) {
        errorMessage = error.message;
      }
      
      Alert.alert(
        translate('Error', 'Vuto'),
        errorMessage
      );
    } finally {
      setLoading(false);
    }
  };

  // Helper function to render icon based on type
  const renderIcon = (type: string, color: string, isSelected: boolean) => {
    const iconColor = isSelected ? '#FFFFFF' : color;
    const config = INCIDENT_TYPES[type as keyof typeof INCIDENT_TYPES];
    
    switch (config.iconSet) {
      case 'material':
        return <MaterialCommunityIcons name={config.icon as any} size={32} color={iconColor} />;
      case 'ion':
        return <Ionicons name={config.icon as any} size={32} color={iconColor} />;
      default:
        return <Ionicons name="help-circle" size={32} color={iconColor} />;
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-gray-50">
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        className="flex-1"
      >
        {/* Header - Updated with purple accent */}
        <View className="bg-white px-4 py-4 border-b border-gray-200">
          <View className="flex-row items-center">
            <TouchableOpacity onPress={() => router.back()} className="mr-4 p-1">
              <Ionicons name="arrow-back" size={24} color="#7C3AED" />
            </TouchableOpacity>
            <View className="flex-1">
              <Text className="text-xl font-bold text-gray-900">
                {translate('Report Harassment', 'Lipoti Zachipongwe')}
              </Text>
              <Text className="text-sm text-gray-500">
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
          {/* Anonymous Switch Section - Updated with purple track color */}
          <View className="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100">
            <View className="flex-row justify-between items-center">
              <View className="flex-1">
                <Text className="text-base font-semibold text-gray-900">
                  {translate('Submit Anonymously', 'Tumiza Mosadziwika')}
                </Text>
                <Text className="text-sm text-gray-500 mt-1">
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
                trackColor={{ false: '#D1D5DB', true: '#7C3AED' }}
                thumbColor="#FFFFFF"
              />
            </View>
          </View>

          {/* Incident Type - NEW CARD DESIGN WITH ICONS */}
          <View className="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100">
            <Text className="text-base font-semibold text-gray-900 mb-3">
              {translate('Type of Harassment', 'Mtundu wa Nkhanza')} <Text className="text-red-500">*</Text>
            </Text>
            <View className="flex-row flex-wrap justify-between gap-3">
              {Object.entries(INCIDENT_TYPES).map(([key, config]) => {
                const isSelected = formData.incident_type === key;
                return (
                  <TouchableOpacity
                    key={key}
                    onPress={() => setFormData((prev) => ({ ...prev, incident_type: key }))}
                    className={`w-[30%] aspect-square rounded-xl items-center justify-center p-3 ${
                      isSelected
                        ? 'bg-[#7C3AED]'
                        : 'bg-gray-50 border border-gray-200'
                    }`}
                    style={{
                      shadowColor: '#000',
                      shadowOffset: { width: 0, height: 2 },
                      shadowOpacity: isSelected ? 0.1 : 0.05,
                      shadowRadius: 4,
                      elevation: isSelected ? 3 : 1,
                    }}
                  >
                    {/* Icon centered */}
                    <View className="items-center justify-center flex-1">
                      {renderIcon(key, config.color, isSelected)}
                    </View>
                    {/* Label below icon */}
                    <Text
                      className={`text-center text-xs font-medium mt-2 ${
                        isSelected ? 'text-white' : 'text-gray-700'
                      }`}
                    >
                      {translate(config.en, config.ny)}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>

          {/* Incident Title */}
          <View className="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100">
            <Text className="text-base font-semibold text-gray-900 mb-2">
              {translate('Incident Title', 'Mutu wa Chochitika')} <Text className="text-red-500">*</Text>
            </Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-gray-50"
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
          <View className="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100">
            <Text className="text-base font-semibold text-gray-900 mb-2">
              {translate('Description of Incident', 'Kufotokozera Chochitika')}{' '}
              <Text className="text-red-500">*</Text>
            </Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-gray-50 min-h-[120px]"
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
            <View className="flex-1 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
              <Text className="text-base font-semibold text-gray-900 mb-2">
                {translate('Date of Incident', 'Tsiku lochitikira')} <Text className="text-red-500">*</Text>
              </Text>
              <TouchableOpacity
                onPress={() => setShowDatePicker(true)}
                className="border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 flex-row items-center justify-between"
              >
                <Text className="text-gray-900">
                  {formData.incident_date || translate('Select Date', 'Sankhani Tsiku')}
                </Text>
                <Ionicons name="calendar-outline" size={20} color="#7C3AED" />
              </TouchableOpacity>
              {errors.incident_date && (
                <Text className="text-red-500 text-sm mt-1">{errors.incident_date}</Text>
              )}
            </View>

            <View className="flex-1 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
              <Text className="text-base font-semibold text-gray-900 mb-2">
                {translate('Location', 'Malo')} <Text className="text-red-500">*</Text>
              </Text>
              <TextInput
                className="border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-gray-50"
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
          <View className="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100">
            <Text className="text-base font-semibold text-gray-900 mb-2">
              {translate('Perpetrator Information', 'Zambiri za Wokuzunza')}
              <Text className="text-sm font-normal text-gray-500">
                {' '}
                ({translate('Optional', 'Mwasankha')})
              </Text>
            </Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-gray-50"
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

          {/* PURPLE SECTION - Contact Information */}
          {!formData.is_anonymous && (
            <View className="bg-violet-50 rounded-xl p-4 mb-4 border border-violet-200">
              <Text className="text-base font-semibold text-violet-800 mb-3">
                {translate('Your Contact Information', 'Zambiri Yanu Yolumikizana')}
              </Text>
              <View className="mb-3">
                <Text className="text-sm font-medium text-violet-700 mb-1">
                  {translate('Full Name', 'Dzina Lathunthu')} <Text className="text-red-500">*</Text>
                </Text>
                <TextInput
                  className="border border-violet-300 rounded-lg px-4 py-3 text-gray-900 bg-white"
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
                <Text className="text-sm font-medium text-violet-700 mb-1">
                  {translate('Email Address', 'Imelo')} <Text className="text-red-500">*</Text>
                </Text>
                <TextInput
                  className="border border-violet-300 rounded-lg px-4 py-3 text-gray-900 bg-white"
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
                <Text className="text-sm font-medium text-violet-700 mb-1">
                  {translate('Phone Number', 'Nambala ya Foni')}
                  <Text className="text-sm font-normal text-violet-600">
                    {' '}({translate('Optional', 'Mwasankha')})
                  </Text>
                </Text>
                <TextInput
                  className="border border-violet-300 rounded-lg px-4 py-3 text-gray-900 bg-white"
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

          {/* GREEN SECTION - Privacy Notice */}
          <View className="bg-green-50 rounded-xl p-4 mb-6 border border-green-200">
            <View className="flex-row items-start">
              <Ionicons name="shield-checkmark" size={20} color="#059669" />
              <Text className="text-sm text-green-700 ml-2 flex-1">
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
            className={`bg-[#7C3AED] rounded-xl py-4 mb-8 ${
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

      {/* Date Picker Modal for iOS */}
      {showDatePicker && Platform.OS === 'ios' && (
        <Modal
          transparent={true}
          animationType="slide"
          visible={showDatePicker}
          onRequestClose={() => setShowDatePicker(false)}
        >
          <View className="flex-1 bg-black/50 justify-end">
            <View className="bg-white rounded-t-3xl p-4">
              <View className="flex-row justify-between items-center mb-4">
                <TouchableOpacity onPress={() => setShowDatePicker(false)}>
                  <Text className="text-[#7C3AED] font-semibold">Cancel</Text>
                </TouchableOpacity>
                <Text className="text-gray-900 font-semibold">
                  {translate('Select Date', 'Sankhani Tsiku')}
                </Text>
                <TouchableOpacity onPress={() => setShowDatePicker(false)}>
                  <Text className="text-[#7C3AED] font-semibold">Done</Text>
                </TouchableOpacity>
              </View>
              <DateTimePicker
                value={selectedDate}
                mode="date"
                display="spinner"
                onChange={onDateChange}
                maximumDate={new Date()}
              />
            </View>
          </View>
        </Modal>
      )}

      {/* Android Date Picker */}
      {showDatePicker && Platform.OS === 'android' && (
        <DateTimePicker
          value={selectedDate}
          mode="date"
          display="default"
          onChange={onDateChange}
          maximumDate={new Date()}
        />
      )}
    </SafeAreaView>
  );
}