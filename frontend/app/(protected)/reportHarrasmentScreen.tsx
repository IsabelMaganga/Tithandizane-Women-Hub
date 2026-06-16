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
  useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { useRouter } from 'expo-router';
import { useLanguage } from '../../context/LanguageContext';
import { submitHarassmentReport, submitAnonymousReport } from '../../services/api';
import { Ionicons, MaterialCommunityIcons, FontAwesome5, Feather } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { LinearGradient } from 'expo-linear-gradient';
import { useThemeToggle } from '../../hooks/useTheme';

// ─── Types ─────────────────────────────────────────────────────────────────────
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

// ─── Incident type options ──────────────────────────────────────────────────────
const INCIDENT_TYPES = [
  {
    key: 'physical',
    en: 'Physical',
    ny: 'Kwakuthupi',
    icon: 'body-outline',
    iconFamily: 'Ionicons',
    color: '#EF4444',
    bgColor: '#FEE2E2',
    darkBg: '#3b0f0f',
  },
  {
    key: 'verbal',
    en: 'Verbal',
    ny: 'Pakamwa',
    icon: 'message-circle',
    iconFamily: 'Feather',
    color: '#F59E0B',
    bgColor: '#FEF3C7',
    darkBg: '#3b2a0f',
  },
  {
    key: 'sexual',
    en: 'Sexual',
    ny: 'Kogonana',
    icon: 'heart-dislike',
    iconFamily: 'Ionicons',
    color: '#EC4899',
    bgColor: '#FCE7F3',
    darkBg: '#3b0f25',
  },
  {
    key: 'cyber',
    en: 'Cyber',
    ny: 'Pa Intaneti',
    icon: 'smartphone',
    iconFamily: 'Feather',
    color: '#06B6D4',
    bgColor: '#CFFAFE',
    darkBg: '#0a2a30',
  },
  {
    key: 'other',
    en: 'Other',
    ny: 'Zina',
    icon: 'help-circle',
    iconFamily: 'Feather',
    color: '#8B5CF6',
    bgColor: '#EDE9FE',
    darkBg: '#2d1b69',
  },
];

// ─── Sub-components defined OUTSIDE the screen to prevent focus loss ─────────
// Defining components inside a render function recreates them as new types on
// every state change, causing React to unmount/remount them and steal focus.

const inputStyle = (isDark: boolean, hasError?: boolean) => ({
  borderWidth: 1.5,
  borderColor: hasError ? '#EF4444' : (isDark ? '#334155' : '#e2e8f0'),
  borderRadius: 12,
  paddingHorizontal: 14,
  paddingVertical: 13,
  fontSize: 14,
  color: isDark ? '#f1f5f9' : '#1a1a2e',
  backgroundColor: isDark ? '#0f172a' : '#fafafa',
});

const Card = ({
  children, isDark, style,
}: {
  children: React.ReactNode; isDark: boolean; style?: any;
}) => (
  <View style={{
    backgroundColor: isDark ? '#1e293b' : '#ffffff',
    borderRadius: 24,
    padding: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: isDark ? '#334155' : '#e2e8f0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: isDark ? 0.25 : 0.08,
    shadowRadius: 12,
    elevation: 4,
    ...style,
  }}>
    {children}
  </View>
);

const FieldLabel = ({
  label, required, hint, isDark,
}: {
  label: string; required?: boolean; hint?: string; isDark: boolean;
}) => (
  <View style={{ marginBottom: 6 }}>
    <Text style={{ fontSize: 14, fontWeight: '600', color: isDark ? '#f1f5f9' : '#2d2d2d' }}>
      {label}{required && <Text style={{ color: '#EF4444' }}> *</Text>}
    </Text>
    {hint && <Text style={{ fontSize: 11, color: isDark ? '#64748b' : '#999', marginTop: 2 }}>{hint}</Text>}
  </View>
);

const SectionHeader = ({
  icon, title, subtitle, isDark,
}: {
  icon: any; title: string; subtitle?: string; isDark: boolean;
}) => (
  <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 16 }}>
    <View style={{
      width: 40, height: 40, borderRadius: 12,
      backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
      alignItems: 'center', justifyContent: 'center', marginRight: 12,
    }}>
      <Ionicons name={icon} size={20} color="#7c3aed" />
    </View>
    <View style={{ flex: 1 }}>
      <Text style={{ fontSize: 16, fontWeight: '700', color: isDark ? '#f1f5f9' : '#1a1a2e' }}>{title}</Text>
      {subtitle && <Text style={{ fontSize: 12, color: isDark ? '#64748b' : '#888', marginTop: 1 }}>{subtitle}</Text>}
    </View>
  </View>
);

const renderIcon = (type: typeof INCIDENT_TYPES[0], size: number, color: string, selected: boolean) => {
  const iconColor = selected ? '#ffffff' : color;
  switch (type.iconFamily) {
    case 'Feather':
      // @ts-ignore
      return <Feather name={type.icon} size={size} color={iconColor} />;
    case 'MaterialCommunityIcons':
      // @ts-ignore
      return <MaterialCommunityIcons name={type.icon} size={size} color={iconColor} />;
    case 'FontAwesome5':
      // @ts-ignore
      return <FontAwesome5 name={type.icon} size={size} color={iconColor} />;
    default:
      // @ts-ignore
      return <Ionicons name={type.icon} size={size} color={iconColor} />;
  }
};

const SquareCard = ({
  type, selected, onPress, language, isDark, cardWidth,
}: {
  type: typeof INCIDENT_TYPES[0];
  selected: boolean;
  onPress: () => void;
  language: string;
  isDark: boolean;
  cardWidth: number;
}) => (
  <TouchableOpacity
    onPress={onPress}
    activeOpacity={0.75}
    style={{
      width: cardWidth,
      aspectRatio: 0.9,
      borderRadius: 16,
      borderWidth: selected ? 2 : 1,
      borderColor: selected ? type.color : (isDark ? '#334155' : '#e2e8f0'),
      backgroundColor: selected
        ? (isDark ? type.darkBg : type.bgColor)
        : (isDark ? '#1e293b' : '#ffffff'),
      alignItems: 'center',
      justifyContent: 'center',
      padding: 8,
      marginBottom: 10,
      shadowColor: selected ? type.color : '#000',
      shadowOffset: { width: 0, height: selected ? 4 : 2 },
      shadowOpacity: selected ? 0.25 : 0.06,
      shadowRadius: selected ? 8 : 4,
      elevation: selected ? 6 : 2,
      transform: [{ scale: selected ? 1.02 : 1 }],
    }}
  >
    <View style={{
      width: 44, height: 44, borderRadius: 12,
      backgroundColor: selected ? type.color : (isDark ? type.darkBg : type.bgColor),
      alignItems: 'center', justifyContent: 'center', marginBottom: 8,
    }}>
      {renderIcon(type, 22, type.color, selected)}
    </View>
    <Text style={{
      fontSize: 11,
      fontWeight: selected ? '700' : '500',
      color: selected ? type.color : (isDark ? '#94a3b8' : '#4a4a4a'),
      textAlign: 'center',
    }}>
      {language === 'en' ? type.en : type.ny}
    </Text>
    {selected && (
      <View style={{
        position: 'absolute', top: 6, right: 6,
        backgroundColor: type.color, borderRadius: 10, padding: 2,
      }}>
        <Ionicons name="checkmark" size={10} color="#ffffff" />
      </View>
    )}
  </TouchableOpacity>
);

// ─── Main Screen ────────────────────────────────────────────────────────────────
export default function HarassmentReportScreen() {
  const router = useRouter();
  const { language } = useLanguage();
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();

  const isDark = colorScheme === 'dark';
  const isTablet = width >= 768;
  // 3-column grid (5 items: row of 3 + row of 2 centred by flexWrap)
  const cardWidth = (width - (isTablet ? 64 : 32) - 2 * 10) / 3;

  const T = {
    bg:         isDark ? '#0f172a' : '#f8fafc',
    text:       isDark ? '#f1f5f9' : '#111827',
    subtext:    isDark ? '#94a3b8' : '#6b7280',
    purpleBg:   isDark ? '#2d1b69' : '#f5f3ff',
    placeholdr: isDark ? '#475569' : '#bdbdbd',
  };

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

  const [errors] = useState<Partial<Record<keyof ReportFormData, string>>>({});

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
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style="light" />

      {/* ── Gradient Header ── */}
      <LinearGradient
        colors={['#7c3aed', '#6d28d9']}
        style={{ paddingBottom: 28, borderBottomLeftRadius: 36, borderBottomRightRadius: 36 }}
      >
        <SafeAreaView edges={['top']}>
          <View style={{ paddingHorizontal: 20, paddingTop: 8, paddingBottom: 4 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 16 }}>
              <TouchableOpacity
                onPress={() => router.back()}
                style={{
                  width: 40, height: 40, borderRadius: 12,
                  backgroundColor: 'rgba(255,255,255,0.2)',
                  alignItems: 'center', justifyContent: 'center',
                }}
              >
                <Ionicons name="arrow-back" size={20} color="#fff" />
              </TouchableOpacity>
              <View style={{
                backgroundColor: 'rgba(255,255,255,0.2)', borderRadius: 10,
                paddingHorizontal: 10, paddingVertical: 5,
              }}>
                <Text style={{ color: '#fff', fontSize: 11, fontWeight: '700', letterSpacing: 1 }}>REPORT</Text>
              </View>
            </View>

            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 14 }}>
              <View style={{ backgroundColor: 'rgba(255,255,255,0.2)', padding: 14, borderRadius: 20 }}>
                <Ionicons name="shield-checkmark" size={28} color="white" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={{ color: '#fff', fontSize: isTablet ? 26 : 22, fontWeight: '900' }}>
                  {t('Report Harassment', 'Lipoti Zachipongwe')}
                </Text>
                <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 13, marginTop: 2 }}>
                  {t('Your report is confidential', 'Lipoti lanu ndi lachinsinsi')}
                </Text>
              </View>
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
        <ScrollView
          style={{ flex: 1 }}
          contentContainerStyle={{
            paddingHorizontal: isTablet ? 32 : 16,
            paddingTop: 20,
            paddingBottom: 48,
          }}
          showsVerticalScrollIndicator={false}
        >

          {/* ── Anonymous / Identified Toggle ── */}
          <Card isDark={isDark}>
            <SectionHeader
              icon="people-outline"
              title={t('Report Anonymously?', 'Lipoti Mosadziwika?')}
              subtitle={t('Choose how you want to report', 'Sankhani njira yomwe mukufuna kulipotira')}
              isDark={isDark}
            />

            <View style={{ flexDirection: 'row', gap: 10 }}>
              <TouchableOpacity
                onPress={() => setFormData(prev => ({ ...prev, is_anonymous: true }))}
                style={{
                  flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
                  paddingVertical: 13, borderRadius: 14, gap: 6,
                  backgroundColor: formData.is_anonymous ? '#7c3aed' : (isDark ? '#0f172a' : '#f5f5f5'),
                  borderWidth: 1.5,
                  borderColor: formData.is_anonymous ? '#7c3aed' : (isDark ? '#334155' : '#e5e7eb'),
                  shadowColor: formData.is_anonymous ? '#7c3aed' : '#000',
                  shadowOffset: { width: 0, height: formData.is_anonymous ? 3 : 1 },
                  shadowOpacity: formData.is_anonymous ? 0.25 : 0.06,
                  shadowRadius: formData.is_anonymous ? 6 : 2,
                  elevation: formData.is_anonymous ? 5 : 1,
                }}
              >
                <Ionicons
                  name="glasses-outline" size={16}
                  color={formData.is_anonymous ? '#fff' : (isDark ? '#64748b' : '#888')}
                />
                <Text style={{
                  fontSize: 13, fontWeight: '600',
                  color: formData.is_anonymous ? '#fff' : (isDark ? '#94a3b8' : '#555'),
                }}>
                  {t('Anonymous', 'Osadziwika')}
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                onPress={() => setFormData(prev => ({ ...prev, is_anonymous: false }))}
                style={{
                  flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
                  paddingVertical: 13, borderRadius: 14, gap: 6,
                  backgroundColor: !formData.is_anonymous ? '#7c3aed' : (isDark ? '#0f172a' : '#f5f5f5'),
                  borderWidth: 1.5,
                  borderColor: !formData.is_anonymous ? '#7c3aed' : (isDark ? '#334155' : '#e5e7eb'),
                  shadowColor: !formData.is_anonymous ? '#7c3aed' : '#000',
                  shadowOffset: { width: 0, height: !formData.is_anonymous ? 3 : 1 },
                  shadowOpacity: !formData.is_anonymous ? 0.25 : 0.06,
                  shadowRadius: !formData.is_anonymous ? 6 : 2,
                  elevation: !formData.is_anonymous ? 5 : 1,
                }}
              >
                <Ionicons
                  name="person-outline" size={16}
                  color={!formData.is_anonymous ? '#fff' : (isDark ? '#64748b' : '#888')}
                />
                <Text style={{
                  fontSize: 13, fontWeight: '600',
                  color: !formData.is_anonymous ? '#fff' : (isDark ? '#94a3b8' : '#555'),
                }}>
                  {t('Identified', 'Ndi Dzina')}
                </Text>
              </TouchableOpacity>
            </View>

            <View style={{
              flexDirection: 'row', alignItems: 'center',
              backgroundColor: T.purpleBg, borderRadius: 12, padding: 12, marginTop: 12,
            }}>
              <Ionicons name="lock-closed-outline" size={15} color="#7c3aed" />
              <Text style={{ fontSize: 12, color: '#7c3aed', marginLeft: 8, flex: 1, lineHeight: 16 }}>
                {formData.is_anonymous
                  ? t(
                      "We won't collect any personally identifiable information from you.",
                      "Sitidzalemba zambiri zanu zonena za inuyo."
                    )
                  : t(
                      'Your contact info will only be used to follow up on your report.',
                      'Zambiri zolumikizana nazo zidzagwiritsidwa ntchito pofunsira lipoti lanu.'
                    )
                }
              </Text>
            </View>
          </Card>

          {/* ── Identity Fields (shown when not anonymous) ── */}
          {!formData.is_anonymous && (
            <Card isDark={isDark}>
              <SectionHeader
                icon="person-outline"
                title={t('Your Information', 'Zambiri Zanu')}
                subtitle={t('Used for follow-up only', 'Zokhagwiritsidwa ntchito pofunsira')}
                isDark={isDark}
              />

              <View style={{ marginBottom: 14 }}>
                <FieldLabel label={t('Full Name', 'Dzina Lanu Lonse')} required isDark={isDark} />
                <TextInput
                  style={inputStyle(isDark, !!errors.victim_name)}
                  placeholder={t('Enter your full name', 'Lowetsani dzina lanu lonse')}
                  placeholderTextColor={T.placeholdr}
                  value={formData.victim_name}
                  onChangeText={text => setFormData(prev => ({ ...prev, victim_name: text }))}
                />
                {errors.victim_name && (
                  <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.victim_name}</Text>
                )}
              </View>

              <View style={{ marginBottom: 14 }}>
                <FieldLabel label={t('Email Address', 'Imelo')} required isDark={isDark} />
                <TextInput
                  style={inputStyle(isDark, !!errors.victim_email)}
                  placeholder={t('Enter your email', 'Lowetsani imelo yanu')}
                  placeholderTextColor={T.placeholdr}
                  keyboardType="email-address"
                  autoCapitalize="none"
                  value={formData.victim_email}
                  onChangeText={text => setFormData(prev => ({ ...prev, victim_email: text }))}
                />
                {errors.victim_email && (
                  <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.victim_email}</Text>
                )}
              </View>

              <View>
                <FieldLabel
                  label={t('Phone Number', 'Nambala ya Foni')}
                  hint={t('Optional', 'Mwasankha')}
                  isDark={isDark}
                />
                <TextInput
                  style={inputStyle(isDark)}
                  placeholder={t('Enter phone number', 'Lowetsani nambala ya foni')}
                  placeholderTextColor={T.placeholdr}
                  keyboardType="phone-pad"
                  value={formData.victim_phone}
                  onChangeText={text => setFormData(prev => ({ ...prev, victim_phone: text }))}
                />
              </View>
            </Card>
          )}

          {/* ── Incident Details ── */}
          <Card isDark={isDark}>
            <SectionHeader
              icon="document-text-outline"
              title={t('Incident Details', 'Zambiri za Chochitika')}
              subtitle={t('Please provide as much detail as you can.', 'Chonde perekani zambiri mwatsatanetsatane.')}
              isDark={isDark}
            />

            {/* Type of Harassment */}
            <View style={{ marginBottom: 20 }}>
              <FieldLabel label={t('Type of Harassment', 'Mtundu wa Nkhanza')} required isDark={isDark} />
              <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' }}>
                {INCIDENT_TYPES.map(type => (
                  <SquareCard
                    key={type.key}
                    type={type}
                    selected={formData.incident_type === type.key}
                    onPress={() => setFormData(prev => ({ ...prev, incident_type: type.key }))}
                    language={language}
                    isDark={isDark}
                    cardWidth={cardWidth}
                  />
                ))}
              </View>
              {errors.incident_type && (
                <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.incident_type}</Text>
              )}
            </View>

            {/* Incident Title */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Incident Title', 'Mutu wa Chochitika')} required isDark={isDark} />
              <TextInput
                style={inputStyle(isDark, !!errors.incident_title)}
                placeholder={t('e.g., Harassment at school', 'Mwachitsanzo, Kuponderezedwa kusukulu')}
                placeholderTextColor={T.placeholdr}
                value={formData.incident_title}
                onChangeText={text => setFormData(prev => ({ ...prev, incident_title: text }))}
              />
              {errors.incident_title && (
                <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.incident_title}</Text>
              )}
            </View>

            {/* Date of Incident */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel label={t('Date of Incident', 'Tsiku yochitika')} required isDark={isDark} />
              <TouchableOpacity
                onPress={() => setShowDatePicker(true)}
                style={{
                  ...inputStyle(isDark, !!errors.incident_date),
                  flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
                } as any}
              >
                <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
                  <Ionicons name="calendar-outline" size={18} color={T.placeholdr} />
                  <Text style={{ fontSize: 14, color: formData.incident_date ? T.text : T.placeholdr }}>
                    {formData.incident_date
                      ? formatDateDisplay(formData.incident_date)
                      : t('Select date', 'Sankhani tsiku')}
                  </Text>
                </View>
                <Ionicons name="chevron-down" size={16} color={T.placeholdr} />
              </TouchableOpacity>
              {errors.incident_date && (
                <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.incident_date}</Text>
              )}
            </View>

            {/* Location */}
            <View style={{ marginBottom: 16 }}>
              <FieldLabel
                label={t('Location of Incident', 'Malo a Chochitika')}
                required
                hint={t('Be specific if possible', 'Khalani othunthu ngati mudatha')}
                isDark={isDark}
              />
              <View style={{ position: 'relative' }}>
                <Ionicons
                  name="location-outline" size={18} color={T.placeholdr}
                  style={{ position: 'absolute', left: 14, top: 14, zIndex: 1 }}
                />
                <TextInput
                  style={{ ...inputStyle(isDark, !!errors.incident_location), paddingLeft: 38 }}
                  placeholder={t('Enter location', 'Lowetsani malo')}
                  placeholderTextColor={T.placeholdr}
                  value={formData.incident_location}
                  onChangeText={text => setFormData(prev => ({ ...prev, incident_location: text }))}
                />
              </View>
              {errors.incident_location && (
                <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.incident_location}</Text>
              )}
            </View>

            {/* Description */}
            <View>
              <FieldLabel
                label={t('Detailed Description', 'Kufotokozera Mwatsatanetsatane')}
                required
                hint={t('Include what happened, who was involved and any relevant information.', 'Lembani zochitika, amene anakhudzidwa, ndi zambiri zinazo.')}
                isDark={isDark}
              />
              <TextInput
                style={{
                  ...inputStyle(isDark, !!errors.incident_description),
                  minHeight: 120, textAlignVertical: 'top', paddingTop: 12,
                }}
                placeholder={t('Describe what happened in detail...', 'Fotokozani mwatsatanetsatane...')}
                placeholderTextColor={T.placeholdr}
                multiline
                numberOfLines={6}
                value={formData.incident_description}
                onChangeText={text => setFormData(prev => ({ ...prev, incident_description: text }))}
              />
              {errors.incident_description && (
                <Text style={{ color: '#EF4444', fontSize: 12, marginTop: 4 }}>{errors.incident_description}</Text>
              )}
            </View>
          </Card>

          {/* ── Additional Information ── */}
          <Card isDark={isDark}>
            <SectionHeader
              icon="attach-outline"
              title={t('Additional Information', 'Zambiri Zowonjezera')}
              subtitle={t('Optional — any extra details that might help.', 'Mwasankha — onjezani zambiri zinazo.')}
              isDark={isDark}
            />
            <FieldLabel
              label={t('Other Information', 'Zambiri zina')}
              hint={t('Perpetrator details, witnesses, or anything else relevant.', 'Zambiri za wokuzunza, mboni, kapena zinazake.')}
              isDark={isDark}
            />
            <TextInput
              style={{ ...inputStyle(isDark), minHeight: 90, textAlignVertical: 'top', paddingTop: 12 }}
              placeholder={t('Add any other helpful information...', 'Onjezani zambiri zina...')}
              placeholderTextColor={T.placeholdr}
              multiline
              numberOfLines={4}
              value={formData.perpetrator_info}
              onChangeText={text => setFormData(prev => ({ ...prev, perpetrator_info: text }))}
            />
          </Card>

          {/* ── Safety Notice ── */}
          <View style={{
            flexDirection: 'row', alignItems: 'center',
            backgroundColor: isDark ? '#0f2918' : '#f0fdf4',
            borderRadius: 16, padding: 14, marginBottom: 16,
            borderWidth: 1, borderColor: isDark ? '#14532d' : '#bbf7d0',
          }}>
            <Ionicons name="shield-checkmark-outline" size={18} color="#16a34a" style={{ marginRight: 10 }} />
            <View style={{ flex: 1 }}>
              <Text style={{ fontSize: 13, fontWeight: '700', color: isDark ? '#4ade80' : '#15803d', marginBottom: 2 }}>
                {t('Your safety is our priority.', 'Kusalama kwanu ndiko kofunika kwambiri.')}
              </Text>
              <Text style={{ fontSize: 12, color: isDark ? '#86efac' : '#166534', lineHeight: 16 }}>
                {t(
                  'All reports are confidential and handled with the utmost care.',
                  'Mauthenga onse ndi achinsinsi ndipo amasungidwa mwachidwi chachikulu.'
                )}
              </Text>
            </View>
          </View>

          {/* ── Submit Button ── */}
          <TouchableOpacity
            onPress={handleSubmit}
            disabled={loading}
            style={{
              backgroundColor: loading ? '#8b5cf6' : '#7c3aed',
              borderRadius: 16, paddingVertical: 16,
              flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
              shadowColor: '#7c3aed', shadowOffset: { width: 0, height: 6 },
              shadowOpacity: 0.35, shadowRadius: 14, elevation: 8,
            }}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <>
                <Ionicons name="send" size={18} color="#fff" />
                <Text style={{ color: '#fff', fontSize: 16, fontWeight: '700' }}>
                  {t('Submit Report', 'Tumizani Lipoti')}
                </Text>
              </>
            )}
          </TouchableOpacity>

        </ScrollView>
      </KeyboardAvoidingView>

      {/* ── iOS Date Picker Modal ── */}
      {showDatePicker && Platform.OS === 'ios' && (
        <Modal transparent animationType="slide" visible onRequestClose={() => setShowDatePicker(false)}>
          <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.55)', justifyContent: 'flex-end' }}>
            <View style={{
              backgroundColor: isDark ? '#1e293b' : '#ffffff',
              borderTopLeftRadius: 28, borderTopRightRadius: 28, padding: 20,
            }}>
              <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 }}>
                <TouchableOpacity onPress={() => setShowDatePicker(false)}>
                  <Text style={{ color: T.subtext, fontWeight: '600' }}>{t('Cancel', 'Lekani')}</Text>
                </TouchableOpacity>
                <Text style={{ fontWeight: '700', color: T.text }}>{t('Select Date', 'Sankhani Tsiku')}</Text>
                <TouchableOpacity onPress={() => setShowDatePicker(false)}>
                  <Text style={{ color: '#7c3aed', fontWeight: '700' }}>{t('Done', 'Chinachita')}</Text>
                </TouchableOpacity>
              </View>
              <DateTimePicker
                value={selectedDate}
                mode="date"
                display="spinner"
                onChange={onDateChange}
                maximumDate={new Date()}
                themeVariant={isDark ? 'dark' : 'light'}
              />
            </View>
          </View>
        </Modal>
      )}

      {/* ── Android Date Picker ── */}
      {showDatePicker && Platform.OS === 'android' && (
        <DateTimePicker
          value={selectedDate}
          mode="date"
          display="default"
          onChange={onDateChange}
          maximumDate={new Date()}
        />
      )}
    </View>
  );
}
