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
  Modal,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { useLanguage } from '../../context/LanguageContext';
import { submitHarassmentReport, submitAnonymousReport } from '../../services/api';
import { Ionicons, MaterialCommunityIcons, FontAwesome5, Feather } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { LinearGradient } from 'expo-linear-gradient';

// ─── Types ────────────────────────────────────────────────────────────────────
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

// ─── Incident type options with unique colors and improved icons ─────────────────
const INCIDENT_TYPES = [
  { 
    key: 'physical', 
    en: 'Physical Harassment',  
    ny: 'Kuzunzidwa Kwakuthupi',   
    icon: 'body-outline',
    iconFamily: 'Ionicons',
    color: '#EF4444', 
    bgColor: '#FEE2E2'
  },
  { 
    key: 'verbal',   
    en: 'Verbal Harassment',    
    ny: 'Kuzunzidwa Pakamwa',      
    icon: 'message-circle',
    iconFamily: 'Feather',
    color: '#F59E0B', 
    bgColor: '#FEF3C7'
  },
  { 
    key: 'sexual',   
    en: 'Sexual Harassment',    
    ny: 'Kuzunzidwa Kogonana',     
    icon: 'heart-dislike',
    iconFamily: 'Ionicons',
    color: '#EC4899', 
    bgColor: '#FCE7F3'
  },
  { 
    key: 'cyber',    
    en: 'Cyber Harassment',     
    ny: 'Kuzunzidwa Pa Intaneti',  
    icon: 'smartphone',
    iconFamily: 'Feather',
    color: '#06B6D4', 
    bgColor: '#CFFAFE'
  },
  { 
    key: 'other',    
    en: 'Other',                
    ny: 'Zina',                    
    icon: 'help-circle',
    iconFamily: 'Feather',
    color: '#8B5CF6', 
    bgColor: '#EDE9FE'
  },
];

const PRIMARY_PURPLE = '#7c3aed';
const PRIMARY_PURPLE_DARK = '#6d28d9';
const PURPLE_LIGHT = '#8b5cf6';
const PURPLE_BG = '#F3E8FF';

// header components

const SectionHeader = ({ icon, title, subtitle }: { icon: any; title: string; subtitle?: string }) => (
  <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 16 }}>
    <View style={{
      width: 40, height: 40, borderRadius: 12,
      backgroundColor: PURPLE_BG, alignItems: 'center', justifyContent: 'center', marginRight: 12,
    }}>
      <Ionicons name={icon} size={20} color={PRIMARY_PURPLE} />
    </View>
    <View style={{ flex: 1 }}>
      <Text style={{ fontSize: 16, fontWeight: '700', color: '#1a1a2e' }}>{title}</Text>
      {subtitle && <Text style={{ fontSize: 12, color: '#888', marginTop: 1 }}>{subtitle}</Text>}
    </View>
  </View>
);

const FieldLabel = ({ label, required, hint }: { label: string; required?: boolean; hint?: string }) => (
  <View style={{ marginBottom: 6 }}>
    <Text style={{ fontSize: 14, fontWeight: '600', color: '#2d2d2d' }}>
      {label}{required && <Text style={{ color: '#EF4444' }}> *</Text>}
    </Text>
    {hint && <Text style={{ fontSize: 11, color: '#999', marginTop: 2 }}>{hint}</Text>}
  </View>
);

const Card = ({ children, style }: { children: React.ReactNode; style?: any }) => (
  <View style={{
    backgroundColor: '#fff',
    borderRadius: 24,
    padding: 16,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 4,
    ...style,
  }}>
    {children}
  </View>
);

const renderIcon = (type: typeof INCIDENT_TYPES[0], size: number, color: string, selected: boolean) => {
  const iconColor = selected ? '#FFFFFF' : color;
  switch (type.iconFamily) {
    case 'Feather':
      return <Feather name={type.icon as any} size={size} color={iconColor} />;
    case 'MaterialCommunityIcons':
      return <MaterialCommunityIcons name={type.icon as any} size={size} color={iconColor} />;
    case 'FontAwesome5':
      return <FontAwesome5 name={type.icon as any} size={size} color={iconColor} />;
    default:
      return <Ionicons name={type.icon as any} size={size} color={iconColor} />;
  }
};

const SquareCard = ({ 
  type, 
  selected, 
  onPress, 
  language
}: { 
  type: typeof INCIDENT_TYPES[0]; 
  selected: boolean; 
  onPress: () => void; 
  language: string;
}) => (
  <TouchableOpacity
    onPress={onPress}
    activeOpacity={0.7}
    style={{
      width: '31%',
      aspectRatio: 0.9,
      borderRadius: 16,
      borderWidth: selected ? 2 : 1,
      borderColor: selected ? type.color : '#E5E7EB',
      backgroundColor: selected ? type.bgColor : '#FFFFFF',
      alignItems: 'center',
      justifyContent: 'center',
      padding: 8,
      marginBottom: 10,
      shadowColor: selected ? type.color : '#000',
      shadowOffset: { width: 0, height: selected ? 4 : 2 },
      shadowOpacity: selected ? 0.25 : 0.08,
      shadowRadius: selected ? 8 : 4,
      elevation: selected ? 6 : 2,
      transform: [{ scale: selected ? 1.02 : 1 }],
    }}
  >
    <View style={{
      width: 44,
      height: 44,
      borderRadius: 12,
      backgroundColor: selected ? type.color : type.bgColor,
      alignItems: 'center',
      justifyContent: 'center',
      marginBottom: 8,
    }}>
      {renderIcon(type, 22, type.color, selected)}
    </View>
    <Text style={{
      fontSize: 11,
      fontWeight: selected ? '700' : '500',
      color: selected ? type.color : '#4A4A4A',
      textAlign: 'center',
    }}>
      {language === 'en' ? type.en : type.ny}
    </Text>
    {selected && (
      <View style={{
        position: 'absolute',
        top: 6,
        right: 6,
        backgroundColor: type.color,
        borderRadius: 10,
        padding: 2,
      }}>
        <Ionicons name="checkmark" size={10} color="#FFFFFF" />
      </View>
    )}
  </TouchableOpacity>
);

const inputStyle = (hasError?: boolean) => ({
  borderWidth: 1.5,
  borderColor: hasError ? '#EF4444' : '#E5E7EB',
  borderRadius: 12,
  paddingHorizontal: 14,
  paddingVertical: 13,
  fontSize: 14,
  color: '#1a1a2e',
  backgroundColor: '#FAFAFA',
});

// ─── MAIN SCREEN ───────────────────────────────────────────────────────────────
export default function HarassmentReportScreen() {
  const router = useRouter();
  const { language } = useLanguage();

  const [loading, setLoading] = useState(false);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [selectedDate, setSelectedDate] = useState(new Date());

  const [formData, setFormData] = useState<ReportFormData>({
    incident_type: '',
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

  const t = (en: string, ny: string) => (language === 'en' ? en : ny);

  const formatDateDisplay = (dateStr: string) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  const onDateChange = (_: any, date?: Date) => {
    if (Platform.OS === 'android') setShowDatePicker(false);
    if (date) {
      setSelectedDate(date);
      setFormData(prev => ({ ...prev, incident_date: date.toISOString().split('T')[0] }));
    }
  };

  const handleSubmit = async () => {
    console.log('📤 Submit button pressed, form data:', formData);
    setLoading(true);
    try {
      let response;
      if (formData.is_anonymous) {
        response = await submitAnonymousReport({
          title: formData.incident_title.trim(),
          description: formData.incident_description.trim(),
          location: formData.incident_location.trim(),
        });
      } else {
        response = await submitHarassmentReport({
          incident_type: formData.incident_type,
          incident_title: formData.incident_title.trim(),
          incident_description: formData.incident_description.trim(),
          incident_location: formData.incident_location.trim(),
          incident_date: formData.incident_date,
          perpetrator_info: formData.perpetrator_info.trim() || null,
          is_anonymous: false,
          victim_name: formData.victim_name.trim(),
          victim_email: formData.victim_email.trim(),
        });
      }

      const ref = response?.data?.reference_number ?? response?.reference_number ?? undefined;
      Alert.alert(
        t('Report Submitted', 'Lipoti Latumizidwa'),
        t(
          `Your report has been submitted successfully.${ref ? `\n\nReference: ${ref}` : ''}\n\nAn administrator will review it shortly.`,
          `Lipoti lanu latumizidwa bwino.${ref ? `\n\nNambala: ${ref}` : ''}\n\nWoyang'anira adzaliwunika posachedwa.`
        ),
        [{ text: t('OK', 'Chabwino'), onPress: () => router.back() }]
      );
    } catch (error: any) {
      let msg = t('Failed to submit. Please try again.', 'Kutumiza kwalephera. Yesaninso.');
      if (error.response?.data?.errors)
        msg = (Object.values(error.response.data.errors) as string[][]).flat().join('\n');
      else if (error.response?.data?.message) msg = error.response.data.message;
      Alert.alert(t('Error', 'Vuto'), msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: '#F9FAFB' }}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>

        {/* Header */}
        <View style={{
          backgroundColor: '#fff',
          paddingHorizontal: 16,
          paddingVertical: 12,
          borderBottomWidth: 1,
          borderBottomColor: '#F0F0F0',
          flexDirection: 'row',
          alignItems: 'center',
          elevation: 3,
        }}>
          <TouchableOpacity
            onPress={() => router.back()}
            style={{
              width: 36, height: 36, borderRadius: 10,
              backgroundColor: PURPLE_BG, alignItems: 'center', justifyContent: 'center', marginRight: 12,
            }}
          >
            <Ionicons name="arrow-back" size={20} color={PRIMARY_PURPLE} />
          </TouchableOpacity>
          <View style={{ flex: 1 }}>
            <Text style={{ fontSize: 17, fontWeight: '700', color: '#1a1a2e' }}>
              {t('Report Harassment', 'Lipoti Zachipongwe')}
            </Text>
            <Text style={{ fontSize: 12, color: '#888' }}>
              {t('Your report is confidential', 'Lipoti lanu ndi lachinsinsi')}
            </Text>
          </View>
        </View>

        <ScrollView style={{ flex: 1, paddingHorizontal: 16, paddingTop: 16 }} showsVerticalScrollIndicator={false}>

          {/* Hero Banner */}
          <LinearGradient
            colors={[PRIMARY_PURPLE, PRIMARY_PURPLE_DARK]}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 1 }}
            style={{ borderRadius: 24, padding: 20, marginBottom: 16, flexDirection: 'row', alignItems: 'center' }}
          >
            <View style={{ flex: 1 }}>
              <Text style={{ fontSize: 20, fontWeight: '800', color: '#fff', marginBottom: 6 }}>
                {t('Report Harassment', 'Lipoti Zachipongwe')}
              </Text>
              <Text style={{ fontSize: 12, color: 'rgba(255,255,255,0.85)', lineHeight: 18 }}>
                {t('Help us build a safer community...', 'Tithandizeni kumanga dera lotetezeka...')}
              </Text>
            </View>
          </LinearGradient>

          {/* Anonymous Toggle */}
          <Card>
            <SectionHeader
              icon="people-outline"
              title={t('Report Anonymously', 'Lipoti Mosadziwika')}
              subtitle={t('Choose how you want to report', 'Sankhani njira yomwe mukufuna kulipotira')}
            />
            <View style={{ flexDirection: 'row', gap: 10 }}>
              <TouchableOpacity
                onPress={() => setFormData(prev => ({ ...prev, is_anonymous: true }))}
                style={{
                  flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
                  paddingVertical: 13, borderRadius: 14, gap: 6,
                  backgroundColor: formData.is_anonymous ? PRIMARY_PURPLE : '#F5F5F5',
                }}
              >
                <Ionicons name="glasses-outline" size={16} color={formData.is_anonymous ? '#fff' : '#888'} />
                <Text style={{ fontSize: 13, fontWeight: '600', color: formData.is_anonymous ? '#fff' : '#555' }}>
                  {t('Stay Anonymous', 'Kkalani osadziwika')}
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                onPress={() => setFormData(prev => ({ ...prev, is_anonymous: false }))}
                style={{
                  flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
                  paddingVertical: 13, borderRadius: 14, gap: 6,
                  backgroundColor: !formData.is_anonymous ? PRIMARY_PURPLE : '#F5F5F5',
                }}
              >
                <Ionicons name="person-outline" size={16} color={!formData.is_anonymous ? '#fff' : '#888'} />
                <Text style={{ fontSize: 13, fontWeight: '600', color: !formData.is_anonymous ? '#fff' : '#555' }}>
                  {t('Share Identity', 'Dziwitsani Dzina')}
                </Text>
              </TouchableOpacity>
            </View>
          </Card>

          {/* Incident Details */}
          <Card>
            <SectionHeader
              icon="document-text-outline"
              title={t('Incident Details', 'Zambiri za Chochitika')}
              subtitle={t('Please provide as much detail as you can.', 'Chonde perekani zambiri mwatsatanetsatane.')}
            />

            {/* Type picker using outer SquareCard component */}
            <View style={{ marginBottom: 20 }}>
              <FieldLabel label={t('Type of Harassment', 'Mtundu wa Nkhanza')} required />
              <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' }}>
                {INCIDENT_TYPES.map(type => (
                  <SquareCard
                    key={type.key}
                    type={type}
                    selected={formData.incident_type === type.key}
                    onPress={() => setFormData(prev => ({ ...prev, incident_type: type.key }))}
                    language={language}
                  />
                ))}
              </View>
            </View>

            {/* Incident Title */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Incident Title', 'Mutu wa Chochitika')} required />
              <TextInput
                style={inputStyle(!!errors.incident_title)}
                placeholder={t('e.g., Harassment at school', 'Mwachitsanzo, Kuponderezedwa kusukulu')}
                placeholderTextColor="#BDBDBD"
                value={formData.incident_title}
                onChangeText={text => setFormData(prev => ({ ...prev, incident_title: text }))}
              />
            </View>

            {/* Date */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Date of Incident', 'Tsiku yochitika')} required />
              <TouchableOpacity
                onPress={() => setShowDatePicker(true)}
                style={{ ...inputStyle(!!errors.incident_date), flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}
              >
                <Text style={{ fontSize: 14, color: formData.incident_date ? '#1a1a2e' : '#BDBDBD' }}>
                  {formData.incident_date ? formatDateDisplay(formData.incident_date) : t('Select date', 'Sankhani tsiku')}
                </Text>
                <Ionicons name="chevron-down" size={16} color="#BDBDBD" />
              </TouchableOpacity>
            </View>

            {/* Location */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Location of Incident', 'Malo a Chochitika')} required />
              <TextInput
                style={inputStyle(!!errors.incident_location)}
                placeholder={t('Enter location', 'Lowetsani malo')}
                placeholderTextColor="#BDBDBD"
                value={formData.incident_location}
                onChangeText={text => setFormData(prev => ({ ...prev, incident_location: text }))}
              />
            </View>

            {/* Description */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Detailed Description', 'Kufotokozera Mwatsatanetsatane')} required />
              <TextInput
                style={{ ...inputStyle(!!errors.incident_description), minHeight: 120, textAlignVertical: 'top' }}
                placeholder={t('Please describe what happened in detail...', 'Chonde fotokozani mwatsatanetsatane...')}
                placeholderTextColor="#BDBDBD"
                multiline
                value={formData.incident_description}
                onChangeText={text => setFormData(prev => ({ ...prev, incident_description: text }))}
              />
            </View>
          </Card>

          {/* Additional Info */}
          <Card>
            <SectionHeader icon="attach-outline" title={t('Additional Information', 'Zambiri Zowonjezera')} />
            <TextInput
              style={{ ...inputStyle(), minHeight: 90, textAlignVertical: 'top' }}
              placeholder={t('Add any other information...', 'Onjezani zambiri zina...')}
              placeholderTextColor="#BDBDBD"
              multiline
              value={formData.perpetrator_info}
              onChangeText={text => setFormData(prev => ({ ...prev, perpetrator_info: text }))}
            />
          </Card>

          {/* Submit Button */}
          <TouchableOpacity
            onPress={handleSubmit}
            disabled={loading}
            style={{ backgroundColor: PRIMARY_PURPLE, borderRadius: 16, paddingVertical: 16, marginBottom: 32, alignItems: 'center' }}
          >
            {loading ? <ActivityIndicator color="#fff" /> : <Text style={{ color: '#fff', fontSize: 16, fontWeight: '700' }}>{t('Submit Report', 'Tumizani Lipoti')}</Text>}
          </TouchableOpacity>

        </ScrollView>
      </KeyboardAvoidingView>

      {/* Modals & Picker Logic remain down here safely */}
      {showDatePicker && (
        <DateTimePicker
          value={selectedDate}
          mode="date"
          display={Platform.OS === 'ios' ? 'spinner' : 'default'}
          onChange={onDateChange}
          maximumDate={new Date()}
        />
      )}
    </SafeAreaView>
  );
}