import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, Pressable, ScrollView,
  ActivityIndicator, Alert, StatusBar,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useThemeToggle } from '../../hooks/useTheme';
import { getReportByReference, ReportTracking } from '../../services/api';

const STATUS_META: Record<string, { label: string; color: string; icon: string }> = {
  pending:   { label: 'Pending Review',  color: '#f59e0b', icon: 'clock-outline' },
  reviewing: { label: 'Under Review',    color: '#3b82f6', icon: 'magnify' },
  assigned:  { label: 'Mentor Assigned', color: '#8b5cf6', icon: 'account-check' },
  resolved:  { label: 'Resolved',        color: '#10b981', icon: 'check-circle' },
  dismissed: { label: 'Dismissed',       color: '#6b7280', icon: 'close-circle' },
};

export default function TrackReportScreen() {
  const { ref, submitted } = useLocalSearchParams<{ ref?: string; submitted?: string }>();
  const router = useRouter();
  const { isDark } = useThemeToggle();

  const [code, setCode] = useState(ref ?? '');
  const [loading, setLoading] = useState(false);
  const [report, setReport] = useState<ReportTracking | null>(null);
  const [notFound, setNotFound] = useState(false);

  const T = {
    bg:     isDark ? '#0f0f1a' : '#f8f7ff',
    card:   isDark ? '#1e1b2e' : '#ffffff',
    border: isDark ? '#2d2a3e' : '#e5e7eb',
    text:   isDark ? '#f3f4f6' : '#111827',
    sub:    isDark ? '#9ca3af' : '#6b7280',
    input:  isDark ? '#2d2a3e' : '#f5f3ff',
    inputBorder: isDark ? '#4c1d95' : '#ddd6fe',
  };

  useEffect(() => {
    if (ref) lookup(ref);
  }, [ref]);

  async function lookup(refCode: string) {
    const trimmed = refCode.trim().toUpperCase();
    if (!trimmed) return;
    setLoading(true);
    setNotFound(false);
    setReport(null);
    try {
      const result = await getReportByReference(trimmed);
      if (result) {
        setReport(result);
      } else {
        setNotFound(true);
      }
    } catch {
      Alert.alert('Error', 'Could not reach the server. Please try again.');
    } finally {
      setLoading(false);
    }
  }

  const meta = report ? (STATUS_META[report.status] ?? STATUS_META.pending) : null;

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar barStyle="light-content" />

      <LinearGradient colors={['#7c3aed', '#6d28d9']} style={{ paddingBottom: 28 }}>
        <SafeAreaView edges={['top']}>
          <View style={{ flexDirection: 'row', alignItems: 'center', paddingHorizontal: 20, paddingTop: 8 }}>
            <Pressable
              onPress={() => router.back()}
              style={{ width: 38, height: 38, borderRadius: 12, backgroundColor: 'rgba(255,255,255,0.15)', justifyContent: 'center', alignItems: 'center' }}
            >
              <Feather name="arrow-left" size={20} color="#fff" />
            </Pressable>
            <Text style={{ color: '#fff', fontSize: 20, fontWeight: '800', marginLeft: 14 }}>
              Track Your Report
            </Text>
          </View>

          <Text style={{ color: '#ddd6fe', fontSize: 13, marginHorizontal: 20, marginTop: 10 }}>
            Enter your reference code to see the status and any mentor response.
          </Text>
        </SafeAreaView>
      </LinearGradient>

      <ScrollView contentContainerStyle={{ padding: 20 }} keyboardShouldPersistTaps="handled">

        {submitted === 'true' && !report && (
          <View style={{ backgroundColor: '#f0fdf4', borderRadius: 14, padding: 16, borderWidth: 1, borderColor: '#bbf7d0', marginBottom: 20 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
              <MaterialCommunityIcons name="check-circle" size={22} color="#16a34a" />
              <Text style={{ color: '#15803d', fontWeight: '700', fontSize: 15 }}>Report Submitted</Text>
            </View>
            <Text style={{ color: '#166534', fontSize: 13, marginTop: 6, lineHeight: 20 }}>
              Your report has been received. Save your reference code below — you'll need it to check back for a mentor's response.
            </Text>
          </View>
        )}

        {ref && (
          <View style={{ backgroundColor: isDark ? '#2d1b69' : '#ede9fe', borderRadius: 14, padding: 16, borderWidth: 1, borderColor: '#7c3aed', marginBottom: 20, alignItems: 'center' }}>
            <Text style={{ color: isDark ? '#c4b5fd' : '#5b21b6', fontSize: 12, fontWeight: '600', marginBottom: 4 }}>YOUR REFERENCE CODE</Text>
            <Text style={{ color: isDark ? '#ede9fe' : '#4c1d95', fontSize: 22, fontWeight: '900', letterSpacing: 3 }}>{ref}</Text>
            <Text style={{ color: isDark ? '#a78bfa' : '#7c3aed', fontSize: 11, marginTop: 6 }}>Keep this private — it's the only way to track your anonymous report</Text>
          </View>
        )}

        {/* Search input */}
        <View style={{ backgroundColor: T.card, borderRadius: 16, padding: 18, borderWidth: 1, borderColor: T.border, marginBottom: 20 }}>
          <Text style={{ color: T.sub, fontSize: 12, fontWeight: '600', marginBottom: 10 }}>ENTER REFERENCE CODE</Text>
          <View style={{ flexDirection: 'row', gap: 10 }}>
            <TextInput
              value={code}
              onChangeText={v => setCode(v.toUpperCase())}
              placeholder="e.g. TWH-2024-XXXXX"
              placeholderTextColor={T.sub}
              autoCapitalize="characters"
              autoCorrect={false}
              style={{
                flex: 1,
                backgroundColor: T.input,
                borderWidth: 1,
                borderColor: T.inputBorder,
                borderRadius: 12,
                paddingHorizontal: 14,
                paddingVertical: 12,
                color: T.text,
                fontSize: 15,
                fontWeight: '700',
                letterSpacing: 1,
              }}
            />
            <Pressable
              onPress={() => lookup(code)}
              disabled={loading || !code.trim()}
              style={{
                backgroundColor: code.trim() ? '#7c3aed' : (isDark ? '#3d3550' : '#e5e7eb'),
                borderRadius: 12,
                paddingHorizontal: 18,
                justifyContent: 'center',
                alignItems: 'center',
              }}
            >
              {loading
                ? <ActivityIndicator color="#fff" size="small" />
                : <Feather name="search" size={20} color={code.trim() ? '#fff' : T.sub} />
              }
            </Pressable>
          </View>
        </View>

        {notFound && (
          <View style={{ backgroundColor: T.card, borderRadius: 16, padding: 24, borderWidth: 1, borderColor: T.border, alignItems: 'center' }}>
            <MaterialCommunityIcons name="file-search-outline" size={48} color={T.sub} />
            <Text style={{ color: T.text, fontWeight: '700', fontSize: 16, marginTop: 14 }}>No Report Found</Text>
            <Text style={{ color: T.sub, textAlign: 'center', marginTop: 8, fontSize: 13, lineHeight: 20 }}>
              Double-check the code and try again. Reference codes are case-sensitive and look like TWH-2024-XXXXX.
            </Text>
          </View>
        )}

        {report && meta && (
          <View style={{ gap: 14 }}>
            {/* Status card */}
            <View style={{ backgroundColor: T.card, borderRadius: 16, padding: 18, borderWidth: 1, borderColor: T.border }}>
              <Text style={{ color: T.sub, fontSize: 11, fontWeight: '700', marginBottom: 12 }}>REPORT STATUS</Text>

              <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
                <View style={{ width: 42, height: 42, borderRadius: 12, backgroundColor: meta.color + '22', justifyContent: 'center', alignItems: 'center' }}>
                  <MaterialCommunityIcons name={meta.icon as any} size={24} color={meta.color} />
                </View>
                <View>
                  <Text style={{ color: meta.color, fontWeight: '800', fontSize: 16 }}>{meta.label}</Text>
                  <Text style={{ color: T.sub, fontSize: 12, marginTop: 2 }}>Submitted {report.submitted_at}</Text>
                </View>
              </View>

              <View style={{ flexDirection: 'row', marginTop: 14, gap: 8 }}>
                <View style={{ flex: 1, backgroundColor: isDark ? '#1a1730' : '#f5f3ff', borderRadius: 10, padding: 10 }}>
                  <Text style={{ color: T.sub, fontSize: 11, fontWeight: '600' }}>TYPE</Text>
                  <Text style={{ color: T.text, fontSize: 13, fontWeight: '700', marginTop: 3, textTransform: 'capitalize' }}>
                    {report.incident_type?.replace(/_/g, ' ')}
                  </Text>
                </View>
                <View style={{ flex: 1, backgroundColor: isDark ? '#1a1730' : '#f5f3ff', borderRadius: 10, padding: 10 }}>
                  <Text style={{ color: T.sub, fontSize: 11, fontWeight: '600' }}>REF CODE</Text>
                  <Text style={{ color: T.text, fontSize: 12, fontWeight: '700', marginTop: 3, letterSpacing: 1 }}>
                    {report.reference_number}
                  </Text>
                </View>
              </View>

              {report.assigned_mentor && (
                <View style={{ marginTop: 12, flexDirection: 'row', alignItems: 'center', gap: 8, backgroundColor: isDark ? '#2d1b69' : '#ede9fe', borderRadius: 10, padding: 10 }}>
                  <MaterialCommunityIcons name="account-heart" size={18} color="#7c3aed" />
                  <Text style={{ color: isDark ? '#c4b5fd' : '#5b21b6', fontSize: 13, fontWeight: '600' }}>
                    Mentor assigned: {report.assigned_mentor.name}
                  </Text>
                </View>
              )}
            </View>

            {/* Mentor response */}
            {report.has_response && report.response ? (
              <View style={{ backgroundColor: T.card, borderRadius: 16, padding: 18, borderWidth: 1, borderColor: T.border }}>
                <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 12 }}>
                  <MaterialCommunityIcons name="message-reply-text" size={20} color="#7c3aed" />
                  <Text style={{ color: T.text, fontWeight: '800', fontSize: 15 }}>Mentor Response</Text>
                </View>
                {report.responded_at && (
                  <Text style={{ color: T.sub, fontSize: 12, marginBottom: 10 }}>Responded on {report.responded_at}</Text>
                )}
                <View style={{ backgroundColor: isDark ? '#1a1730' : '#f5f3ff', borderRadius: 12, padding: 14, borderLeftWidth: 3, borderLeftColor: '#7c3aed' }}>
                  <Text style={{ color: T.text, fontSize: 14, lineHeight: 22 }}>{report.response}</Text>
                </View>
              </View>
            ) : (
              <View style={{ backgroundColor: T.card, borderRadius: 16, padding: 18, borderWidth: 1, borderColor: T.border, alignItems: 'center' }}>
                <MaterialCommunityIcons name="clock-outline" size={36} color={T.sub} />
                <Text style={{ color: T.text, fontWeight: '700', fontSize: 15, marginTop: 10 }}>Awaiting Response</Text>
                <Text style={{ color: T.sub, textAlign: 'center', fontSize: 13, marginTop: 6, lineHeight: 20 }}>
                  A mentor will review your report and write a response here. Check back in 1–2 business days.
                </Text>
              </View>
            )}
          </View>
        )}
      </ScrollView>
    </View>
  );
}
