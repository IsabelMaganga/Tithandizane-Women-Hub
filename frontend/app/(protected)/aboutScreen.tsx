import React from 'react';
import { View, Text, Image, ScrollView, Pressable, Linking, useWindowDimensions } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather, MaterialCommunityIcons, FontAwesome5, Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useThemeToggle } from '../../hooks/useTheme';
import { LinearGradient } from 'expo-linear-gradient';

const PILLARS = [
  {
    icon: 'account-group',     family: 'MC',  color: '#ec4899', bg: '#fce7f3',
    title: 'Safe Community',
    desc:  'Engage in meaningful conversations with other women and mentors in a moderated, supportive environment.',
  },
  {
    icon: 'book-reader',       family: 'FA5', color: '#2563eb', bg: '#dbeafe',
    title: 'Knowledge Hub',
    desc:  'Access verified information about hygiene, schooling, and women\'s rights to help you navigate life\'s challenges.',
  },
  {
    icon: 'shield-alert',      family: 'MC',  color: '#dc2626', bg: '#fee2e2',
    title: 'Safety & Reporting',
    desc:  'Quickly report harassment and find emergency contact numbers for immediate help and legal counseling.',
  },
  {
    icon: 'hands-helping',     family: 'FA5', color: '#7c3aed', bg: '#ede9fe',
    title: 'Mentorship',
    desc:  'Connect with experienced mentors who guide you through personal, academic, and professional challenges.',
  },
] as const;

const SOCIALS = [
  { icon: 'facebook', color: '#1877F2', url: 'https://facebook.com/tithandizane' },
  { icon: 'instagram', color: '#E4405F', url: 'https://instagram.com/tithandizane' },
  { icon: 'mail', color: '#7C3AED', url: 'mailto:support@tithandizane.com' },
] as const;

export default function AboutScreen() {
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const isDark = colorScheme === 'dark';
  const isTablet = width >= 768;

  const T = {
    bg:      isDark ? '#0f172a' : '#ffffff',
    card:    isDark ? '#1e293b' : '#f8fafc',
    border:  isDark ? '#334155' : '#e2e8f0',
    text:    isDark ? '#f1f5f9' : '#111827',
    subtext: isDark ? '#94a3b8' : '#6b7280',
    footer:  isDark ? '#1e293b' : '#f8fafc',
  };

  const openLink = (url: string) => Linking.openURL(url).catch(() => {});

  const PillarIcon = ({ icon, family, color, size = 22 }: any) =>
    family === 'FA5'
      ? <FontAwesome5 name={icon} size={size} color={color} />
      : <MaterialCommunityIcons name={icon} size={size + 2} color={color} />;

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style={isDark ? 'light' : 'dark'} />

      {/* Header */}
      <View style={{
        flexDirection: 'row', alignItems: 'center',
        paddingHorizontal: 16, paddingVertical: 14,
        borderBottomWidth: 1, borderBottomColor: T.border,
      }}>
        <Pressable
          onPress={() => router.back()}
          style={({ pressed }) => ({
            width: 38, height: 38, borderRadius: 12,
            backgroundColor: isDark ? '#334155' : '#f1f5f9',
            alignItems: 'center', justifyContent: 'center', opacity: pressed ? 0.7 : 1,
          })}
        >
          <Feather name="arrow-left" size={18} color={T.text} />
        </Pressable>
        <Text style={{ fontSize: 17, fontWeight: '800', color: T.text, marginLeft: 12 }}>About Us</Text>
      </View>

      <ScrollView showsVerticalScrollIndicator={false}>

        {/* Hero */}
        <LinearGradient
          colors={isDark ? ['#2d1b69', '#1e1b4b'] : ['#f5f3ff', '#ede9fe']}
          style={{ alignItems: 'center', paddingVertical: isTablet ? 56 : 40, paddingHorizontal: 24 }}
        >
          <View style={{
            width: 88, height: 88, backgroundColor: isDark ? '#1e293b' : '#fff',
            borderRadius: 28, shadowColor: '#7c3aed',
            shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.2, shadowRadius: 14, elevation: 6,
            alignItems: 'center', justifyContent: 'center', marginBottom: 16,
          }}>
            <Image
              source={require('../../assets/images/Ellipse 3.png')}
              style={{ width: 60, height: 60 }}
              resizeMode="contain"
            />
          </View>
          <Text style={{ fontSize: isTablet ? 26 : 22, fontWeight: '900', color: '#7c3aed', textAlign: 'center' }}>
            Tithandizane Women Hub
          </Text>
          <Text style={{ color: isDark ? '#a78bfa' : '#7c3aed', fontSize: 12, marginTop: 4,
            letterSpacing: 2, textTransform: 'uppercase', fontWeight: '700' }}>
            Empowering Every Woman
          </Text>
        </LinearGradient>

        {/* Mission */}
        <View style={{ paddingHorizontal: 24, paddingVertical: 28 }}>
          <Text style={{ fontSize: 20, fontWeight: '800', color: T.text, marginBottom: 12 }}>Our Mission</Text>
          <View style={{
            backgroundColor: isDark ? '#1e2d3d' : '#f0f9ff',
            borderRadius: 18, padding: 18,
            borderLeftWidth: 4, borderLeftColor: '#7c3aed',
          }}>
            <Text style={{ color: isDark ? '#bae6fd' : '#0369a1', fontSize: 14, lineHeight: 22, fontStyle: 'italic' }}>
              {'"'}Tithandizane{'"'} means {'"'}Let us help each other.{'"'} We are a dedicated safe space designed to uplift women through mentorship, peer support, and essential education on hygiene and health.
            </Text>
          </View>
        </View>

        {/* Pillars */}
        <View style={{ paddingHorizontal: 24 }}>
          <Text style={{ fontSize: 20, fontWeight: '800', color: T.text, marginBottom: 16 }}>What We Offer</Text>
          <View style={{ gap: 14 }}>
            {PILLARS.map((p, i) => (
              <View key={i} style={{
                backgroundColor: isDark ? '#1e293b' : '#fff',
                borderRadius: 20, padding: 18,
                flexDirection: 'row', alignItems: 'flex-start', gap: 14,
                borderWidth: 1, borderColor: T.border,
                shadowColor: p.color, shadowOffset: { width: 0, height: 2 },
                shadowOpacity: isDark ? 0.1 : 0.06, shadowRadius: 6, elevation: 2,
              }}>
                <View style={{
                  width: 48, height: 48, borderRadius: 16,
                  backgroundColor: isDark ? `${p.color}22` : p.bg,
                  alignItems: 'center', justifyContent: 'center',
                }}>
                  <PillarIcon icon={p.icon} family={p.family} color={p.color} />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={{ fontSize: 15, fontWeight: '800', color: T.text, marginBottom: 4 }}>{p.title}</Text>
                  <Text style={{ color: T.subtext, fontSize: 13, lineHeight: 19 }}>{p.desc}</Text>
                </View>
              </View>
            ))}
          </View>
        </View>

        {/* Socials / footer */}
        <View style={{
          marginTop: 36, backgroundColor: T.footer,
          borderTopLeftRadius: 36, borderTopRightRadius: 36,
          paddingHorizontal: 24, paddingTop: 28, paddingBottom: 40,
          borderTopWidth: 1, borderTopColor: T.border,
        }}>
          <Text style={{ textAlign: 'center', fontWeight: '800', color: T.text, marginBottom: 20, fontSize: 16 }}>
            Connect With Us
          </Text>
          <View style={{ flexDirection: 'row', justifyContent: 'center', gap: 20, marginBottom: 28 }}>
            {SOCIALS.map(s => (
              <Pressable
                key={s.icon}
                onPress={() => openLink(s.url)}
                style={({ pressed }) => ({
                  width: 52, height: 52, borderRadius: 26,
                  backgroundColor: isDark ? '#334155' : '#fff',
                  alignItems: 'center', justifyContent: 'center',
                  shadowColor: s.color,
                  shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.2, shadowRadius: 6, elevation: 3,
                  opacity: pressed ? 0.75 : 1,
                })}
              >
                <Feather name={s.icon as any} size={22} color={s.color} />
              </Pressable>
            ))}
          </View>

          <View style={{
            backgroundColor: isDark ? '#1e293b' : '#fff',
            borderRadius: 16, padding: 14, alignItems: 'center',
            borderWidth: 1, borderColor: T.border,
          }}>
            <Ionicons name="shield-checkmark" size={18} color="#10b981" style={{ marginBottom: 6 }} />
            <Text style={{ color: T.subtext, fontSize: 12, textAlign: 'center', lineHeight: 18 }}>
              All data is protected and handled with the utmost care.
            </Text>
          </View>

          <Text style={{ textAlign: 'center', color: T.subtext, fontSize: 11, marginTop: 20 }}>
            Version 1.0.0
          </Text>
          <Text style={{ textAlign: 'center', color: T.subtext, fontSize: 11, marginTop: 2 }}>
            © 2026 Tithandizane Women Hub. All Rights Reserved.
          </Text>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}
