import React, { useState, useCallback } from 'react';
import {
  View, Text, Pressable, ScrollView,
  ActivityIndicator, RefreshControl, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import { useThemeToggle } from '../../hooks/useTheme';
import { getMentorReports, HarassmentReport } from '../../services/api';

const STATUS_META: Record<string, { label: string; color: string; icon: string }> = {
  pending:   { label: 'Pending',         color: '#f59e0b', icon: 'clock-outline' },
  reviewing: { label: 'Under Review',    color: '#3b82f6', icon: 'magnify' },
  assigned:  { label: 'Mentor Assigned', color: '#8b5cf6', icon: 'account-check' },
  resolved:  { label: 'Resolved',        color: '#10b981', icon: 'check-circle' },
  dismissed: { label: 'Dismissed',       color: '#6b7280', icon: 'close-circle' },
};

function ReportCard({ report, isDark, T, onViewDetails }: {
  report: HarassmentReport;
  isDark: boolean;
  T: Record<string, string>;
  onViewDetails?: (report: HarassmentReport) => void;
}) {
  const [expanded, setExpanded] = useState(false);
  const meta = STATUS_META[report.status] ?? STATUS_META.pending;

  const handleCardPress = () => {
    if (onViewDetails) {
      return onViewDetails(report);
    }
    setExpanded(e => !e);
  };

  return (
    <View style={{ backgroundColor: T.card, borderRadius: 18, borderWidth: 1, borderColor: T.border, marginBottom: 14, overflow: 'hidden' }}>
      <Pressable onPress={handleCardPress} style={{ padding: 16 }}>
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

          {onViewDetails && (
            <Pressable
              onPress={() => onViewDetails(report)}
              style={{
                marginTop: 10,
                backgroundColor: '#7c3aed',
                paddingVertical: 12,
                borderRadius: 14,
                alignItems: 'center',
              }}
            >
              <Text style={{ color: '#fff', fontWeight: '700' }}>Open report</Text>
            </Pressable>
          )}
        </View>
      )}
    </View>
  );
}

export default function MentorReportsScreen() {
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const isDark = colorScheme === 'dark';
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
      const data = await getMentorReports();
      setReports(data);
    } catch {
      Alert.alert('Error', 'Failed to load mentor reports. Please try again.');
    }
  }

  useFocusEffect(
    useCallback(() => {
      setLoading(true);
      fetchReports().finally(() => setLoading(false));
    }, [])
  );

  async function onRefresh() {
    setRefreshing(true);
    await fetchReports();
    setRefreshing(false);
  }

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
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
              <Text style={{ color: '#fff', fontSize: 20, fontWeight: '800' }}>Mentor Reports</Text>
              {reports.length > 0 && (
                <Text style={{ color: '#ddd6fe', fontSize: 12, marginTop: 2 }}>{reports.length} report{reports.length !== 1 ? 's' : ''} assigned</Text>
              )}
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      {loading ? (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
          <ActivityIndicator size="large" color="#7c3aed" />
          <Text style={{ color: T.sub, marginTop: 12 }}>Loading your assigned reports…</Text>
        </View>
      ) : (
        <ScrollView
          contentContainerStyle={{ padding: 20, paddingBottom: 40 }}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#7c3aed" />}
        >
          {reports.length === 0 ? (
            <View style={{ alignItems: 'center', marginTop: 60 }}>
              <MaterialCommunityIcons name="file-document-outline" size={56} color={T.sub} />
              <Text style={{ color: T.text, fontWeight: '700', fontSize: 17, marginTop: 16 }}>No Assigned Reports Yet</Text>
              <Text style={{ color: T.sub, textAlign: 'center', marginTop: 8, fontSize: 14, lineHeight: 22 }}>
                Reports assigned to you will appear here. Refresh to check for new cases.
              </Text>
            </View>
          ) : (
            reports.map(r => (
              <ReportCard
                key={r.id ?? r.reference_number}
                report={r}
                isDark={isDark}
                T={T}
                onViewDetails={(report) => router.push({ pathname: '/(protected)/ReportDetailsScreen', params: { id: report.id.toString() } })}
              />
            ))
          )}
        </ScrollView>
      )}
    </View>
  );
}
