import React, { useEffect, useState, useMemo } from 'react';
import {
  View, Text, Pressable, Image, TextInput, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { getActiveMentors } from '@/services/api';
import { LegendList } from '@legendapp/list';
import { FontAwesome5, Feather, MaterialCommunityIcons, Ionicons } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { useTranslation } from 'react-i18next';
import LottieView from 'lottie-react-native';
import { useRouter } from 'expo-router';
import { BackButton } from '@/components/BackButton';
import { useThemeToggle } from '../../hooks/useTheme';
import { LinearGradient } from 'expo-linear-gradient';

type Mentor = {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  location: string | null;
  photo: string | null;
  avatar?: string;
  expertise: string[];
  bio: string;
  availability: string | null;
  available_days: string[];
  available_time_start: string | null;
  available_time_end: string | null;
  linkedin_url: string | null;
  twitter_url: string | null;
  website_url: string | null;
  rating?: number | null;
  total_sessions?: number;
  status?: string;
};

// Heart rating – defined outside component to avoid remount on state change
const HeartRating = ({ rating, isDark }: { rating: number | null | undefined; isDark: boolean }) => {
  if (!rating) {
    return (
      <View style={{
        backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
        paddingHorizontal: 8, paddingVertical: 3, borderRadius: 8,
      }}>
        <Text style={{ color: '#7c3aed', fontSize: 11, fontWeight: '700' }}>New Mentor</Text>
      </View>
    );
  }
  return (
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 2 }}>
      {[1, 2, 3, 4, 5].map(i => (
        <FontAwesome5 key={i} name="heart" size={11} color="#7c3aed" solid={i <= Math.round(rating)} />
      ))}
      <Text style={{ color: isDark ? '#94a3b8' : '#64748b', fontSize: 11, fontWeight: '700', marginLeft: 4 }}>
        ({Number(rating).toFixed(1)})
      </Text>
    </View>
  );
};

export default function MentorshipScreen() {
  const [mentors, setMentors]         = useState<Mentor[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading]         = useState(true);
  const { t } = useTranslation();
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const isDark = colorScheme === 'dark';
  const isTablet = width >= 768;

  const T = {
    bg:      isDark ? '#0f172a' : '#f8fafc',
    card:    isDark ? '#1e293b' : '#ffffff',
    border:  isDark ? '#334155' : '#e2e8f0',
    divider: isDark ? '#334155' : '#f1f5f9',
    text:    isDark ? '#f1f5f9' : '#0f172a',
    subtext: isDark ? '#94a3b8' : '#64748b',
    tag:     isDark ? '#334155' : '#f1f5f9',
    tagTxt:  isDark ? '#94a3b8' : '#64748b',
  };

  useEffect(() => { fetchMentors(); }, []);

  const fetchMentors = async () => {
    try {
      setLoading(true);
      const data = await getActiveMentors();
      const safe = Array.isArray(data) ? data : [];
      if (!safe.length) Toast.show({ type: 'info', text1: 'No mentors available', position: 'top' });
      setMentors(safe);
    } catch (error: any) {
      Toast.show({ type: 'error', text1: 'Error', text2: error?.message || 'Failed to load mentors', position: 'top' });
      setMentors([]);
    } finally {
      setLoading(false);
    }
  };

  const filteredMentors = useMemo(() => {
    if (!searchQuery.trim()) return mentors;
    const q = searchQuery.toLowerCase().trim();
    return mentors.filter(m =>
      m?.name?.toLowerCase().includes(q) ||
      m?.bio?.toLowerCase().includes(q) ||
      (Array.isArray(m?.expertise) && m.expertise.some(e => e?.toLowerCase().includes(q)))
    );
  }, [searchQuery, mentors]);

  if (loading) {
    return (
      <View style={{ flex: 1, backgroundColor: T.bg, alignItems: 'center', justifyContent: 'center' }}>
        <StatusBar style={isDark ? 'light' : 'dark'} />
        <LottieView source={require('../../assets/animations/loading.json')} autoPlay loop style={{ width: 150, height: 150 }} />
        <Text style={{ color: T.subtext, fontWeight: '500', marginTop: 8 }}>Finding experts…</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style="light" />

      {/* Header */}
      <LinearGradient
        colors={['#7c3aed', '#6d28d9']}
        style={{ paddingBottom: 28, borderBottomLeftRadius: 36, borderBottomRightRadius: 36 }}
      >
        <SafeAreaView edges={['top']}>
          <View style={{ paddingHorizontal: 20, paddingTop: 8 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 20 }}>
              <BackButton />
              <Text style={{ color: '#fff', fontSize: isTablet ? 22 : 18, fontWeight: '800' }}>
                {t('Expert Mentors')}
              </Text>
              <Pressable
                onPress={() => router.push('/(protected)/sessionsDashboard')}
                style={({ pressed }) => ({
                  flexDirection: 'row', alignItems: 'center', gap: 6,
                  backgroundColor: 'rgba(255,255,255,0.2)', paddingHorizontal: 12, paddingVertical: 8,
                  borderRadius: 14, borderWidth: 1, borderColor: 'rgba(255,255,255,0.25)',
                  opacity: pressed ? 0.8 : 1,
                })}
              >
                <MaterialCommunityIcons name="calendar-clock" size={16} color="#fff" />
                <Text style={{ color: '#fff', fontWeight: '700', fontSize: 12 }}>My Sessions</Text>
              </Pressable>
            </View>

            <Text style={{ color: 'rgba(255,255,255,0.75)', fontSize: 13, marginBottom: 14 }}>
              Connect with leaders to guide your journey
            </Text>

            {/* Search */}
            <View style={{
              flexDirection: 'row', alignItems: 'center',
              backgroundColor: 'rgba(255,255,255,0.15)',
              paddingHorizontal: 16, paddingVertical: 12, borderRadius: 18,
              borderWidth: 1, borderColor: 'rgba(255,255,255,0.2)',
            }}>
              <Feather name="search" size={18} color="rgba(255,255,255,0.7)" />
              <TextInput
                style={{ flex: 1, marginLeft: 10, color: '#fff', fontSize: 14, padding: 0 }}
                placeholder="Search by name, skill…"
                placeholderTextColor="rgba(255,255,255,0.55)"
                value={searchQuery}
                onChangeText={setSearchQuery}
                autoCapitalize="none"
                autoCorrect={false}
              />
              {searchQuery.length > 0 && (
                <Pressable onPress={() => setSearchQuery('')}>
                  <Feather name="x" size={16} color="rgba(255,255,255,0.7)" />
                </Pressable>
              )}
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      <LegendList
        data={filteredMentors}
        estimatedItemSize={280}
        keyExtractor={item => item?.id?.toString() || Math.random().toString()}
        extraData={isDark}
        style={{ backgroundColor: T.bg }}
        contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
        renderItem={({ item }) => {
          if (!item) return null;
          const expertiseStr = Array.isArray(item.expertise) && item.expertise.length
            ? item.expertise.join(', ') : 'Mentor';
          const avatarUrl = item.avatar || item.photo ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name || 'M')}&background=8b5cf6&color=fff`;

          return (
            <View style={{
              backgroundColor: T.card, borderRadius: 28, marginBottom: 16,
              borderWidth: 1, borderColor: T.border,
              shadowColor: '#7c3aed',
              shadowOffset: { width: 0, height: 4 }, shadowOpacity: isDark ? 0.15 : 0.07,
              shadowRadius: 12, elevation: 3, overflow: 'hidden',
            }}>
              <View style={{ padding: isTablet ? 24 : 18 }}>
                {/* Identity */}
                <View style={{ flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between' }}>
                  <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
                    <View style={{ position: 'relative' }}>
                      <Image
                        source={{ uri: avatarUrl }}
                        style={{ width: 68, height: 68, borderRadius: 20, backgroundColor: T.divider }}
                      />
                      <View style={{
                        position: 'absolute', bottom: -2, right: -2,
                        width: 16, height: 16, borderRadius: 8,
                        backgroundColor: '#10b981', borderWidth: 2, borderColor: T.card,
                      }} />
                    </View>

                    <View style={{ marginLeft: 14, flex: 1 }}>
                      <Text style={{ color: T.text, fontSize: 17, fontWeight: '800' }} numberOfLines={1}>
                        {item.name}
                      </Text>
                      <View style={{ marginTop: 5 }}>
                        <HeartRating rating={item.rating} isDark={isDark} />
                      </View>
                      <View style={{
                        backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
                        alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 3,
                        borderRadius: 8, marginTop: 6,
                      }}>
                        <Text style={{ color: '#7c3aed', fontSize: 10, fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.5 }} numberOfLines={1}>
                          {expertiseStr}
                        </Text>
                      </View>
                    </View>
                  </View>

                  <Pressable style={({ pressed }) => ({
                    backgroundColor: T.tag, padding: 8, borderRadius: 12, opacity: pressed ? 0.7 : 1,
                  })}>
                    <Feather name="bookmark" size={16} color={T.subtext} />
                  </Pressable>
                </View>

                {/* Bio */}
                {item.bio ? (
                  <Text style={{ color: T.subtext, fontSize: 13, marginTop: 14, lineHeight: 20 }} numberOfLines={3}>
                    {item.bio}
                  </Text>
                ) : null}

                {/* Availability */}
                <View style={{
                  marginTop: 14, paddingTop: 14,
                  borderTopWidth: 1, borderTopColor: T.divider,
                }}>
                  <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 10 }}>
                    <MaterialCommunityIcons name="calendar-clock" size={16} color="#7c3aed" />
                    <Text style={{ color: T.subtext, fontSize: 12, marginLeft: 6 }}>
                      {item.available_time_start || '09:00'} – {item.available_time_end || '17:00'}
                    </Text>
                  </View>

                  {Array.isArray(item.available_days) && item.available_days.length > 0 && (
                    <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 6 }}>
                      {item.available_days.map((day, i) => (
                        <View key={i} style={{
                          backgroundColor: T.tag, paddingHorizontal: 10, paddingVertical: 4, borderRadius: 10,
                        }}>
                          <Text style={{ color: T.tagTxt, fontSize: 10, fontWeight: '600', textTransform: 'capitalize' }}>{day}</Text>
                        </View>
                      ))}
                    </View>
                  )}
                </View>

                {/* Book button */}
                <Pressable
                  onPress={() => router.push({ pathname: '/mentorship-request', params: { mentorId: item.id.toString(), mentorName: item.name } })}
                  style={({ pressed }) => ({
                    marginTop: 16, paddingVertical: 14, borderRadius: 20,
                    backgroundColor: '#7c3aed',
                    alignItems: 'center', justifyContent: 'center',
                    opacity: pressed ? 0.85 : 1,
                    shadowColor: '#7c3aed', shadowOffset: { width: 0, height: 4 },
                    shadowOpacity: 0.3, shadowRadius: 8, elevation: 4,
                  })}
                >
                  <Text style={{ color: '#fff', fontWeight: '800', fontSize: 15 }}>Book Free Session</Text>
                </Pressable>
              </View>
            </View>
          );
        }}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', marginTop: 60, paddingHorizontal: 40 }}>
            <View style={{
              width: 80, height: 80, borderRadius: 40,
              backgroundColor: isDark ? '#1e293b' : '#f1f5f9',
              alignItems: 'center', justifyContent: 'center', marginBottom: 16,
            }}>
              <FontAwesome5 name="user-slash" size={32} color={isDark ? '#475569' : '#cbd5e1'} />
            </View>
            <Text style={{ color: T.text, fontWeight: '800', fontSize: 17 }}>
              {searchQuery ? 'No Matches Found' : 'No Mentors Yet'}
            </Text>
            <Text style={{ color: T.subtext, textAlign: 'center', marginTop: 8, lineHeight: 20 }}>
              {searchQuery
                ? 'Try adjusting your search or spelling.'
                : "We're onboarding new experts. Check back soon!"}
            </Text>
            <Pressable
              onPress={() => { setSearchQuery(''); fetchMentors(); }}
              style={({ pressed }) => ({
                marginTop: 20, backgroundColor: '#7c3aed',
                paddingHorizontal: 24, paddingVertical: 12, borderRadius: 999,
                opacity: pressed ? 0.85 : 1,
              })}
            >
              <Text style={{ color: '#fff', fontWeight: '700' }}>{searchQuery ? 'Clear Search' : 'Refresh'}</Text>
            </Pressable>
          </View>
        }
      />
    </View>
  );
}
