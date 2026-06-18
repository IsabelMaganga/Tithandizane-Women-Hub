import React, { useState, useCallback } from 'react';
import {
  View, Text, Pressable, ScrollView,
  ActivityIndicator, RefreshControl, StatusBar, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import { useThemeToggle } from '../../hooks/useTheme';
import { getMyReports, createConversation, HarassmentReport } from '../../services/api';
import AsyncStorage from '@react-native-async-storage/async-storage';

const STATUS_META: Record<string, { label: string; color: string; icon: string }> = {
  pending:   { label: 'Pending',         color: '#f59e0b', icon: 'clock-outline' },
  reviewing: { label: 'Under Review',    color: '#3b82f6', icon: 'magnify' },
  assigned:  { label: 'Mentor Assigned', color: '#8b5cf6', icon: 'account-check' },
  resolved:  { label: 'Resolved',        color: '#10b981', icon: 'check-circle' },
  dismissed: { label: 'Dismissed',       color: '#6b7280', icon: 'close-circle' },
};

function ReportCard({ report, onChat, isDark, T }: {
  report: HarassmentReport;
  onChat: (report: HarassmentReport) => void;
  isDark: boolean;
  T: Record<string, string>;
}) {
  const [expanded, setExpanded] = useState(false);
  const meta = STATUS_META[report.status] ?? STATUS_META.pending;
  const canChat = !report.is_anonymous && report.assigned_mentor;

  return (
    <View style={{ backgroundColor: T.card, borderRadius: 18, borderWidth: 1, borderColor: T.border, marginBottom: 14, overflow: 'hidden' }}>
      <Pressable onPress={() => setExpanded(e => !e)} style={{ padding: 16 }}>
        <View style={{ flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between', gap: 10 }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: T.text, fontWeight: '800', fontSize: 15 }} numberOfLines={2}>
              {report.incident_title}
            </Text>
            <Text style={{ color: T.sub, fontSize: 12, marginTop: 4, textTransform: 'capitalize' }}>
              {report.incident_type?.replace(/_/g, ' ')} · {report.submitted_at ?? report.created_at?.slice(0, 10)}
            </Text>
          </View>
          <View style={{ alignItems: 'flex-end', gap: 6 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 5, backgroundColor: meta.color + '22', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 }}>
              <MaterialCommunityIcons name={meta.icon as any} size={13} color={meta.color} />
              <Text style={{ color: meta.color, fontWeight: '700', fontSize: 11 }}>{meta.label}</Text>
            </View>
            <Feather name={expanded ? 'chevron-up' : 'chevron-down'} size={16} color={T.sub} />
          </View>
        </View>

        {report.has_response && (
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 10, backgroundColor: isDark ? '#2d1b69' : '#ede9fe', borderRadius: 8, padding: 8 }}>
            <MaterialCommunityIcons name="message-reply" size={14} color="#7c3aed" />
            <Text style={{ color: isDark ? '#c4b5fd' : '#7c3aed', fontSize: 12, fontWeight: '600' }}>Mentor response available</Text>
          </View>
        )}
      </Pressable>

      {expanded && (
        <View style={{ paddingHorizontal: 16, paddingBottom: 16, gap: 12 }}>
          {report.reference_number && (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
              <Feather name="hash" size={13} color={T.sub} />
              <Text style={{ color: T.sub, fontSize: 12 }}>Ref: <Text style={{ fontWeight: '700', color: T.text }}>{report.reference_number}</Text></Text>
            </View>
          )}

          {report.assigned_mentor && (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8, backgroundColor: isDark ? '#1a1730' : '#f5f3ff', borderRadius: 10, padding: 10 }}>
              <MaterialCommunityIcons name="account-heart" size={16} color="#7c3aed" />
              <Text style={{ color: isDark ? '#c4b5fd' : '#5b21b6', fontSize: 13 }}>
                Assigned mentor: <Text style={{ fontWeight: '700' }}>{report.assigned_mentor.name}</Text>
              </Text>
            </View>
          )}

          {report.has_response && (report.response ?? report.admin_response) ? (
            <View>
              <Text style={{ color: T.sub, fontSize: 11, fontWeight: '700', marginBottom: 8 }}>MENTOR RESPONSE</Text>
              {report.responded_at && (
                <Text style={{ color: T.sub, fontSize: 11, marginBottom: 6 }}>Responded {report.responded_at}</Text>
              )}
              <View style={{ backgroundColor: isDark ? '#1a1730' : '#f5f3ff', borderRadius: 12, padding: 12, borderLeftWidth: 3, borderLeftColor: '#7c3aed' }}>
                <Text style={{ color: T.text, fontSize: 13, lineHeight: 21 }}>
                  {report.response ?? report.admin_response}
                </Text>
              </View>
            </View>
          ) : (
            <View style={{ alignItems: 'center', paddingVertical: 10 }}>
              <Text style={{ color: T.sub, fontSize: 13 }}>No response yet — check back soon.</Text>
            </View>
          )}

          {canChat && (
            <Pressable
              onPress={() => onChat(report)}
              style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: '#7c3aed', borderRadius: 12, paddingVertical: 12 }}
            >
              <MaterialCommunityIcons name="chat" size={18} color="#fff" />
              <Text style={{ color: '#fff', fontWeight: '700', fontSize: 14 }}>Chat with Mentor</Text>
            </Pressable>
          )}
        </View>
      )}
    </View>
  );
}

export default function MyReportsScreen() {
  const router = useRouter();
  const { isDark } = useThemeToggle();
  const [reports, setReports] = useState<HarassmentReport[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const T = {
    bg:     isDark ? '#0f0f1a' : '#f8f7ff',
    card:   isDark ? '#1e1b2e' : '#ffffff',
    border: isDark ? '#2d2a3e' : '#e5e7eb',
    text:   isDark ? '#f3f4f6' : '#111827',
    sub:    isDark ? '#9ca3af' : '#6b7280',
  };

  async function fetchReports() {
    try {
      const data = await getMyReports();
      setReports(data);
    } catch (e: any) {
      Alert.alert('Error', 'Failed to load reports. Please try again.');
    }
  }

  useFocusEffect(
    useCallback(() => {
      setLoading(true);
      fetchReports().finally(() => setLoading(false));
    }, [])
  );

  async function handleChat(report: HarassmentReport) {
    if (!report.assigned_mentor) return;
    try {
      const token = await AsyncStorage.getItem('token');
      if (!token) { Alert.alert('Not logged in'); return; }
      const convo = await createConversation({ target_user_id: report.assigned_mentor.id });
      router.push({ pathname: '/(protected)/chat/[id]', params: { id: convo.id.toString(), name: report.assigned_mentor.name } });
    } catch {
      Alert.alert('Error', 'Could not open chat. Please try again.');
    }
  }

  async function onRefresh() {
    setRefreshing(true);
    await fetchReports();
    setRefreshing(false);
  }

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
            <View style={{ marginLeft: 14 }}>
              <Text style={{ color: '#fff', fontSize: 20, fontWeight: '800' }}>My Reports</Text>
              {reports.length > 0 && (
                <Text style={{ color: '#ddd6fe', fontSize: 12, marginTop: 2 }}>{reports.length} report{reports.length !== 1 ? 's' : ''} submitted</Text>
              )}
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      {loading ? (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
          <ActivityIndicator size="large" color="#7c3aed" />
          <Text style={{ color: T.sub, marginTop: 12 }}>Loading your reports…</Text>
        </View>
      ) : (
        <ScrollView
          contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#7c3aed" />}
        >
          {reports.length === 0 ? (
            <View style={{ alignItems: 'center', marginTop: 60 }}>
              <MaterialCommunityIcons name="file-document-outline" size={56} color={T.sub} />
              <Text style={{ color: T.text, fontWeight: '700', fontSize: 17, marginTop: 16 }}>No Reports Yet</Text>
              <Text style={{ color: T.sub, textAlign: 'center', marginTop: 8, fontSize: 14, lineHeight: 22 }}>
                Reports you submit will appear here. You can track their status and read mentor responses.
              </Text>
              <Pressable
                onPress={() => router.push('/(protected)/reportHarrasmentScreen')}
                style={{ marginTop: 24, backgroundColor: '#7c3aed', borderRadius: 14, paddingHorizontal: 24, paddingVertical: 13 }}
              >
                <Text style={{ color: '#fff', fontWeight: '700', fontSize: 14 }}>Submit a Report</Text>
              </Pressable>
            </View>
          ) : (
            reports.map(r => (
              <ReportCard
                key={r.id ?? r.reference_number}
                report={r}
                onChat={handleChat}
                isDark={isDark}
                T={T}
              />
            ))
          )}
        </ScrollView>
      )}
    </View>
  );
}
