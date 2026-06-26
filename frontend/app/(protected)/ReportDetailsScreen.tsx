import React, { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Pressable,
  ScrollView,
  Text,
  View,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather } from '@expo/vector-icons';
import { useThemeToggle } from '../../hooks/useTheme';
import { createConversation, getMentorReportById, HarassmentReport } from '../../services/api';

export default function ReportDetailsScreen() {
  const { id } = useLocalSearchParams<{ id?: string }>();
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const isDark = colorScheme === 'dark';
  const [report, setReport] = useState<HarassmentReport | null>(null);
  const [loading, setLoading] = useState(true);
  const [startingChat, setStartingChat] = useState(false);

  useEffect(() => {
    if (!id) return;

    const reportId = Number(id);
    if (Number.isNaN(reportId)) {
      Alert.alert('Invalid report', 'Unable to open this report.');
      router.back();
      return;
    }

    async function loadReport() {
      try {
        const data = await getMentorReportById(reportId);
        if (!data) {
          Alert.alert('Not found', 'This report could not be loaded.');
          router.back();
          return;
        }
        setReport(data);
      } catch {
        Alert.alert('Error', 'Unable to load report details.');
        router.back();
      } finally {
        setLoading(false);
      }
    }

    loadReport();
  }, [id, router]);

  async function handleStartChat() {
    if (!report?.user?.id) {
      Alert.alert('No reporter available', 'Cannot start chat for this report.');
      return;
    }

    setStartingChat(true);
    try {
      const conversation = await createConversation({ target_user_id: report.user.id });
      router.push({ pathname: '/(protected)/chat/[id]', params: { id: conversation.id.toString() } });
    } catch {
      Alert.alert('Error', 'Unable to start chat at this time.');
    } finally {
      setStartingChat(false);
    }
  }

  const colors = {
    bg: isDark ? '#09090b' : '#f8fafc',
    card: isDark ? '#111827' : '#ffffff',
    border: isDark ? '#273449' : '#e5e7eb',
    text: isDark ? '#f8fafc' : '#111827',
    sub: isDark ? '#94a3b8' : '#6b7280',
  };

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: colors.bg }}>
        <ActivityIndicator size="large" color="#7c3aed" />
        <Text style={{ color: colors.sub, marginTop: 12 }}>Loading report details…</Text>
      </View>
    );
  }

  if (!report) {
    return null;
  }

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <LinearGradient colors={['#7c3aed', '#6d28d9']} style={{ paddingBottom: 20 }}>
        <SafeAreaView edges={['top']}>
          <View style={{ flexDirection: 'row', alignItems: 'center', paddingHorizontal: 20, paddingTop: 8 }}>
            <Pressable
              onPress={() => router.back()}
              style={{ width: 38, height: 38, borderRadius: 12, backgroundColor: 'rgba(255,255,255,0.15)', justifyContent: 'center', alignItems: 'center' }}
            >
              <Feather name="arrow-left" size={20} color="#fff" />
            </Pressable>
            <View style={{ marginLeft: 14 }}>
              <Text style={{ color: '#fff', fontSize: 20, fontWeight: '800' }}>Report details</Text>
              <Text style={{ color: '#d8b4fe', fontSize: 13, marginTop: 2 }}>Assigned report flow for mentor</Text>
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      <ScrollView contentContainerStyle={{ padding: 20, paddingBottom: 36 }}>
        <View style={{ borderRadius: 22, backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border, padding: 20, gap: 16 }}>
          <View>
            <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Incident title</Text>
            <Text style={{ color: colors.text, fontSize: 18, fontWeight: '800' }}>{report.incident_title}</Text>
          </View>

          <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 10 }}>
            <View style={{ backgroundColor: isDark ? '#1f2937' : '#eef2ff', borderRadius: 999, paddingHorizontal: 12, paddingVertical: 8 }}>
              <Text style={{ color: isDark ? '#c7d2fe' : '#4338ca', fontSize: 12, fontWeight: '700' }}>{report.incident_type?.replace(/_/g, ' ')}</Text>
            </View>
            <View style={{ backgroundColor: isDark ? '#1f2937' : '#eef2ff', borderRadius: 999, paddingHorizontal: 12, paddingVertical: 8 }}>
              <Text style={{ color: isDark ? '#c7d2fe' : '#4338ca', fontSize: 12, fontWeight: '700' }}>{report.status.toUpperCase()}</Text>
            </View>
          </View>

          <View>
            <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Description</Text>
            <Text style={{ color: colors.text, fontSize: 14, lineHeight: 22 }}>{report.incident_description || 'No description provided.'}</Text>
          </View>

          {report.incident_location ? (
            <View>
              <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Location</Text>
              <Text style={{ color: colors.text, fontSize: 14 }}>{report.incident_location}</Text>
            </View>
          ) : null}

          {report.incident_date ? (
            <View>
              <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Incident date</Text>
              <Text style={{ color: colors.text, fontSize: 14 }}>{report.incident_date}</Text>
            </View>
          ) : null}

          {report.perpetrator_info ? (
            <View>
              <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Perpetrator info</Text>
              <Text style={{ color: colors.text, fontSize: 14 }}>{report.perpetrator_info}</Text>
            </View>
          ) : null}

          {report.reference_number ? (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
              <Feather name="hash" size={14} color={colors.sub} />
              <Text style={{ color: colors.sub, fontSize: 12 }}>Reference number: <Text style={{ color: colors.text, fontWeight: '700' }}>{report.reference_number}</Text></Text>
            </View>
          ) : null}

          {report.user ? (
            <View style={{ borderRadius: 18, backgroundColor: isDark ? '#1f2937' : '#f8fafc', padding: 16 }}>
              <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Reported by</Text>
              <Text style={{ color: colors.text, fontSize: 14, fontWeight: '700' }}>{report.user.name ?? 'Anonymous user'}</Text>
              {report.user.phone && <Text style={{ color: colors.sub, fontSize: 13, marginTop: 4 }}>{report.user.phone}</Text>}
            </View>
          ) : null}

          {report.has_response && (report.response ?? report.admin_response) ? (
            <View style={{ borderRadius: 18, backgroundColor: isDark ? '#111827' : '#f8fafc', borderWidth: 1, borderColor: isDark ? '#374151' : '#e5e7eb', padding: 16 }}>
              <Text style={{ color: colors.sub, fontSize: 12, fontWeight: '700', marginBottom: 8 }}>Mentor response</Text>
              <Text style={{ color: colors.text, fontSize: 14, lineHeight: 22 }}>{report.response ?? report.admin_response}</Text>
            </View>
          ) : null}

          <Pressable
            onPress={handleStartChat}
            disabled={startingChat}
            style={{
              marginTop: 10,
              backgroundColor: '#7c3aed',
              borderRadius: 16,
              paddingVertical: 14,
              alignItems: 'center',
            }}
          >
            <Text style={{ color: '#fff', fontWeight: '700' }}>{startingChat ? 'Starting chat…' : 'Chat with reporter'}</Text>
          </Pressable>
        </View>
      </ScrollView>
    </View>
  );
}
