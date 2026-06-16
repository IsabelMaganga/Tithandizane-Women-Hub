import React from 'react';
import { StatusBar } from 'expo-status-bar';
import {
  View, Text, TouchableOpacity, ScrollView, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import Profile from '../Profile';
import { useTranslation } from 'react-i18next';
import { useThemeToggle } from '../../hooks/useTheme';
import {
  MaterialCommunityIcons, MaterialIcons, FontAwesome6, Ionicons, Feather,
} from '@expo/vector-icons';
import Animated, { FadeInDown, FadeInRight, BounceIn, FadeIn } from 'react-native-reanimated';
import { LinearGradient } from 'expo-linear-gradient';

// ─── Colour palette ───────────────────────────────────────────────────────────
const C = {
  purple:  '#7c3aed',
  purpleD: '#6d28d9',
  purpleL: '#a78bfa',
  rose:    '#f43f5e',
  emerald: '#10b981',
  amber:   '#f59e0b',
  cyan:    '#06b6d4',
};

// ─── Static data ──────────────────────────────────────────────────────────────
const SERVICES = [
  { title: 'Mentorship',       icon: 'hands-holding-child', color: C.purple,  family: 'FA6', route: '/mentorshipScreen'        },
  { title: 'Emergency',        icon: 'contact-emergency',   color: C.rose,    family: 'MI',  route: '/emergencyScreen'         },
  { title: 'Health',           icon: 'calendar-days',       color: C.emerald, family: 'FA6', route: '/menstrualHealthScreen'   },
  { title: 'Report Incident',  icon: 'report-problem',      color: C.amber,   family: 'MI',  route: '/reportHarrasmentScreen' },
] as const;

const QUICK_STATS = [
  { label: 'Mentors',   value: '24+', icon: 'account-group'            },
  { label: 'Community', value: '120', icon: 'forum'                    },
  { label: 'Guides',    value: '18',  icon: 'book-open-page-variant'   },
] as const;

// ─── Icon renderer ─────────────────────────────────────────────────────────────
const SvcIcon = ({ icon, family, color, size = 26 }: { icon: string; family: string; color: string; size?: number }) =>
  family === 'FA6'
    ? <FontAwesome6  name={icon as any} size={size} color={color} />
    : <MaterialIcons name={icon as any} size={size} color={color} />;

// ─── Main component ───────────────────────────────────────────────────────────
export default function UserDashboard() {
  const router        = useRouter();
  const { user }      = useAuth();
  const { colorScheme } = useThemeToggle();
  const { t }         = useTranslation();
  const { width }     = useWindowDimensions();
  const isDark        = colorScheme === 'dark';

  // Layout breakpoints
  const isTablet  = width >= 768;
  const numCols   = isTablet ? 4 : 2;
  const cardWidth = (width - 48 - (numCols - 1) * 12) / numCols; // 48px h-pad, 12px gap

  // Theme tokens — no hardcoded colours in JSX below
  const T = {
    bg:         isDark ? '#0f172a' : '#f8fafc',
    card:       isDark ? '#1e293b' : '#ffffff',
    cardBorder: isDark ? '#334155' : '#e2e8f0',
    text:       isDark ? '#f1f5f9' : '#0f172a',
    subtext:    isDark ? '#94a3b8' : '#64748b',
    pillBg:     isDark ? '#334155' : '#f1f5f9',
    shieldBg:   isDark ? '#1a2e1a' : '#f0fdf4',
    shieldBdr:  isDark ? '#166534' : '#bbf7d0',
    shieldIcon: isDark ? '#166534' : '#dcfce7',
  };

  const iconBg = (color: string) => isDark ? `${color}26` : `${color}18`;

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style={isDark ? 'light' : 'dark'} />

      {/* ── Decorative header arc ── */}
      <View style={{ position: 'absolute', top: 0, left: 0, right: 0, height: isTablet ? 260 : 220, overflow: 'hidden' }}>
        <LinearGradient
          colors={isDark ? [C.purpleD, '#0f172a'] : [C.purple, '#c4b5fd']}
          style={{ flex: 1, borderBottomLeftRadius: 48, borderBottomRightRadius: 48 }}
        />
      </View>

      <SafeAreaView style={{ flex: 1 }}>

        {/* ── Top nav ─────────────────────────────────────────────────────── */}
        <Animated.View
          entering={FadeIn.delay(80)}
          style={{
            flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
            paddingHorizontal: 24, paddingTop: 8, paddingBottom: 4,
          }}
        >
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
            <TouchableOpacity
              onPress={() => router.push('/settingsScreen')}
              style={{ borderRadius: 999, borderWidth: 2, borderColor: 'rgba(255,255,255,0.45)' }}
              activeOpacity={0.8}
            >
              <Profile />
            </TouchableOpacity>

            <View>
              <Text style={{ color: 'rgba(255,255,255,0.75)', fontSize: 10, fontWeight: '700',
                letterSpacing: 1.5, textTransform: 'uppercase' }}>
                {t('welcome_back')}
              </Text>
              <Text style={{ color: '#ffffff', fontSize: isTablet ? 22 : 18, fontWeight: '900' }}>
                {user?.name?.split(' ')[0] ?? 'Sister'} 👋
              </Text>
            </View>
          </View>

          <TouchableOpacity
            onPress={() => router.push('/notificationScreen')}
            activeOpacity={0.8}
            style={{
              width: 44, height: 44, borderRadius: 14,
              backgroundColor: 'rgba(255,255,255,0.18)',
              alignItems: 'center', justifyContent: 'center',
              borderWidth: 1, borderColor: 'rgba(255,255,255,0.25)',
            }}
          >
            <Ionicons name="notifications-outline" size={22} color="#ffffff" />
            {/* Notification dot */}
            <View style={{
              position: 'absolute', top: 9, right: 9,
              width: 8, height: 8, borderRadius: 4,
              backgroundColor: C.rose, borderWidth: 1.5, borderColor: T.card,
            }} />
          </TouchableOpacity>
        </Animated.View>

        <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 120 }}>

          {/* ── Quick stats strip ────────────────────────────────────────── */}
          <Animated.View
            entering={FadeInDown.delay(150)}
            style={{ flexDirection: 'row', gap: 10, paddingHorizontal: 24, marginTop: 20 }}
          >
            {QUICK_STATS.map((s, i) => (
              <View
                key={i}
                style={{
                  flex: 1, borderRadius: 18, paddingVertical: 14, paddingHorizontal: 10,
                  backgroundColor: 'rgba(255,255,255,0.15)',
                  borderWidth: 1, borderColor: 'rgba(255,255,255,0.2)',
                  alignItems: 'center',
                }}
              >
                <MaterialCommunityIcons name={s.icon as any} size={18} color="#fff" />
                <Text style={{ color: '#fff', fontSize: 17, fontWeight: '800', marginTop: 4 }}>{s.value}</Text>
                <Text style={{ color: 'rgba(255,255,255,0.7)', fontSize: 10, fontWeight: '600',
                  textTransform: 'uppercase', letterSpacing: 0.5, marginTop: 1 }}>
                  {s.label}
                </Text>
              </View>
            ))}
          </Animated.View>

          {/* ── Featured insight card ─────────────────────────────────────── */}
          <Animated.View entering={FadeInDown.delay(250)} style={{ paddingHorizontal: 24, marginTop: 28 }}>
            <LinearGradient
              colors={[C.purple, C.purpleD]}
              start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
              style={{
                borderRadius: 28, padding: isTablet ? 28 : 22,
                shadowColor: C.purple,
                shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.35, shadowRadius: 16, elevation: 8,
              }}
            >
              <View style={{ flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between' }}>
                <View style={{ flex: 1, marginRight: 12 }}>
                  <View style={{
                    alignSelf: 'flex-start', backgroundColor: 'rgba(255,255,255,0.22)',
                    borderRadius: 8, paddingHorizontal: 8, paddingVertical: 3, marginBottom: 10,
                  }}>
                    <Text style={{ color: '#fff', fontSize: 10, fontWeight: '800', letterSpacing: 1.2 }}>DAILY TIP</Text>
                  </View>
                  <Text style={{ color: '#fff', fontSize: isTablet ? 17 : 14, fontWeight: '700', lineHeight: 22 }}>
                    {'"'}Your strength is in your sisterhood.{'\n'}Reach out to a mentor today.{'"'}
                  </Text>
                </View>
                <View style={{
                  backgroundColor: 'rgba(255,255,255,0.18)', padding: 14,
                  borderRadius: 20, borderWidth: 1, borderColor: 'rgba(255,255,255,0.25)',
                }}>
                  <MaterialCommunityIcons name="comment-quote" size={26} color="white" />
                </View>
              </View>

              <TouchableOpacity
                onPress={() => router.push('/mentorshipScreen')}
                activeOpacity={0.8}
                style={{
                  marginTop: 18, paddingVertical: 13, borderRadius: 16,
                  backgroundColor: 'rgba(255,255,255,0.18)',
                  flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
                  borderWidth: 1, borderColor: 'rgba(255,255,255,0.28)',
                }}
              >
                <Text style={{ color: '#fff', fontWeight: '700', fontSize: 14 }}>Talk to a Mentor</Text>
                <Feather name="arrow-right" size={16} color="white" />
              </TouchableOpacity>
            </LinearGradient>
          </Animated.View>

          {/* ── Community entry ──────────────────────────────────────────── */}
          <Animated.View entering={FadeInRight.delay(350)} style={{ paddingHorizontal: 24, marginTop: 18 }}>
            <TouchableOpacity
              activeOpacity={0.8}
              onPress={() => router.push('/(protected)/community')}
              style={{
                backgroundColor: T.card, borderRadius: 24, padding: 18,
                flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
                borderWidth: 1, borderColor: T.cardBorder,
                shadowColor: isDark ? '#000' : C.purple,
                shadowOffset: { width: 0, height: 4 }, shadowOpacity: isDark ? 0.3 : 0.08,
                shadowRadius: 12, elevation: 4,
              }}
            >
              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <LinearGradient
                  colors={[C.purple, C.purpleL]}
                  style={{ width: 50, height: 50, borderRadius: 18, alignItems: 'center', justifyContent: 'center' }}
                >
                  <Ionicons name="people" size={24} color="white" />
                </LinearGradient>
                <View style={{ marginLeft: 14 }}>
                  <Text style={{ color: T.text, fontWeight: '800', fontSize: 15 }}>Community Hub</Text>
                  <Text style={{ color: T.subtext, fontSize: 12, marginTop: 2 }}>
                    <Text style={{ color: C.emerald, fontWeight: '700' }}>● </Text>
                    120 women active now
                  </Text>
                </View>
              </View>

              <View style={{ backgroundColor: T.pillBg, borderRadius: 12, padding: 8 }}>
                <Feather name="chevron-right" size={18} color={T.subtext} />
              </View>
            </TouchableOpacity>
          </Animated.View>

          {/* ── Services grid ─────────────────────────────────────────────── */}
          <View style={{ paddingHorizontal: 24, marginTop: 28 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 16 }}>
              <Text style={{ color: T.text, fontWeight: '900', fontSize: isTablet ? 22 : 19 }}>Services</Text>
              <View style={{ width: 40, height: 3, borderRadius: 2, backgroundColor: C.purple, opacity: 0.6 }} />
            </View>

            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 12 }}>
              {SERVICES.map((svc, i) => (
                <Animated.View
                  key={svc.title}
                  entering={FadeInDown.delay(380 + i * 70)}
                  style={{ width: cardWidth }}
                >
                  <TouchableOpacity
                    activeOpacity={0.75}
                    onPress={() => router.push(svc.route as any)}
                    style={{
                      backgroundColor: T.card, borderRadius: 24,
                      paddingVertical: isTablet ? 28 : 22,
                      paddingHorizontal: 12,
                      alignItems: 'center',
                      borderWidth: 1, borderColor: T.cardBorder,
                      shadowColor: svc.color,
                      shadowOffset: { width: 0, height: 4 },
                      shadowOpacity: isDark ? 0.18 : 0.1,
                      shadowRadius: 10, elevation: 3,
                    }}
                  >
                    <View style={{
                      backgroundColor: iconBg(svc.color),
                      padding: isTablet ? 18 : 14, borderRadius: 20, marginBottom: 12,
                    }}>
                      <SvcIcon icon={svc.icon} family={svc.family} color={svc.color} size={isTablet ? 30 : 26} />
                    </View>
                    <Text style={{ color: T.text, fontWeight: '700', fontSize: isTablet ? 14 : 12, textAlign: 'center' }}>
                      {svc.title}
                    </Text>
                    {/* Colour accent underline */}
                    <View style={{ width: 28, height: 3, borderRadius: 2, backgroundColor: svc.color, marginTop: 8, opacity: 0.55 }} />
                  </TouchableOpacity>
                </Animated.View>
              ))}
            </View>
          </View>

          {/* ── Safety notice ─────────────────────────────────────────────── */}
          <Animated.View entering={FadeInDown.delay(650)} style={{ paddingHorizontal: 24, marginTop: 24 }}>
            <View style={{
              backgroundColor: T.shieldBg, borderRadius: 20, padding: 16,
              flexDirection: 'row', alignItems: 'center', gap: 14,
              borderWidth: 1, borderColor: T.shieldBdr,
            }}>
              <View style={{ backgroundColor: T.shieldIcon, padding: 10, borderRadius: 14 }}>
                <Ionicons name="shield-checkmark" size={22} color={C.emerald} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={{ color: C.emerald, fontWeight: '800', fontSize: 13 }}>You are in a safe space</Text>
                <Text style={{ color: T.subtext, fontSize: 12, marginTop: 3, lineHeight: 17 }}>
                  All conversations are private and encrypted.
                </Text>
              </View>
            </View>
          </Animated.View>

        </ScrollView>

        {/* ── Floating FAQ / AI bot ─────────────────────────────────────── */}
        <Animated.View entering={BounceIn.delay(900)} style={{ position: 'absolute', bottom: 32, right: 24 }}>
          <TouchableOpacity
            activeOpacity={0.85}
            onPress={() => router.push('/askScreen' as any)}
            style={{
              width: 64, height: 64, borderRadius: 32,
              backgroundColor: T.card,
              alignItems: 'center', justifyContent: 'center',
              shadowColor: C.purple,
              shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.35, shadowRadius: 14, elevation: 10,
              borderWidth: 1.5, borderColor: T.cardBorder,
            }}
          >
            <MaterialCommunityIcons
              name="robot-excited"
              size={26}
              color={isDark ? C.purpleL : C.purple}
            />
            <Text style={{ color: T.subtext, fontSize: 8, fontWeight: '800',
              letterSpacing: 0.5, textTransform: 'uppercase', marginTop: 1 }}>
              {t('FAQ')}
            </Text>
          </TouchableOpacity>
        </Animated.View>

      </SafeAreaView>
    </View>
  );
}
