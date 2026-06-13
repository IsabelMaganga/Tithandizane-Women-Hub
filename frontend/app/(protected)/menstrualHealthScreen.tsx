import React, { useState } from 'react';
import {
  View, Text, ScrollView, Pressable, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Ionicons, MaterialCommunityIcons, Feather } from '@expo/vector-icons';
import { useThemeToggle } from '../../hooks/useTheme';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import Animated, { FadeInDown } from 'react-native-reanimated';

const CYCLE_PHASES = [
  {
    name: 'Menstrual',    days: '1–5',   color: '#f43f5e', bg: '#fce7f3',
    icon: 'water',        tip: 'Rest, stay warm, and drink plenty of water. Mild exercise like walking can help ease cramps.',
  },
  {
    name: 'Follicular',   days: '6–13',  color: '#f59e0b', bg: '#fef3c7',
    icon: 'flower',       tip: 'Energy rises. Great time for learning, socialising, and starting new projects.',
  },
  {
    name: 'Ovulation',    days: '14',    color: '#10b981', bg: '#d1fae5',
    icon: 'star-circle',  tip: 'Peak energy and mood. Fertility is highest. Good time for important conversations.',
  },
  {
    name: 'Luteal',       days: '15–28', color: '#8b5cf6', bg: '#ede9fe',
    icon: 'moon-waning-crescent', tip: 'Slow down gradually. Prioritise rest, nutritious food, and self-care routines.',
  },
] as const;

const HYGIENE_TIPS = [
  { icon: 'water-outline',             text: 'Change sanitary products every 4–8 hours.' },
  { icon: 'hand-left-outline',         text: 'Wash hands before and after handling products.' },
  { icon: 'shirt-outline',             text: 'Wear breathable, cotton underwear to prevent infections.' },
  { icon: 'nutrition-outline',         text: 'Eat iron-rich foods to replenish during your period.' },
  { icon: 'medical-outline',           text: 'Seek a doctor if you experience unusually heavy flow or severe pain.' },
] as const;

export default function MenstrualHealthScreen() {
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const router = useRouter();
  const isDark  = colorScheme === 'dark';
  const isTablet = width >= 768;
  const numCols  = isTablet ? 4 : 2;
  const colW     = (width - 48 - (numCols - 1) * 12) / numCols;

  const [activePhase, setActivePhase] = useState<number | null>(null);

  const T = {
    bg:      isDark ? '#0f172a' : '#f8fafc',
    card:    isDark ? '#1e293b' : '#ffffff',
    border:  isDark ? '#334155' : '#e2e8f0',
    text:    isDark ? '#f1f5f9' : '#111827',
    subtext: isDark ? '#94a3b8' : '#64748b',
    tipBg:   isDark ? '#1e293b' : '#ffffff',
  };

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style="light" />

      {/* Header gradient */}
      <LinearGradient
        colors={['#7c3aed', '#6d28d9']}
        style={{ borderBottomLeftRadius: 36, borderBottomRightRadius: 36, overflow: 'hidden' }}
      >
        <SafeAreaView edges={['top']}>
          <View style={{ paddingHorizontal: 20, paddingTop: 8, paddingBottom: 28 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 20 }}>
              <Pressable
                onPress={() => router.back()}
                style={({ pressed }) => ({
                  width: 40, height: 40, borderRadius: 12,
                  backgroundColor: 'rgba(255,255,255,0.2)',
                  alignItems: 'center', justifyContent: 'center', opacity: pressed ? 0.7 : 1,
                })}
              >
                <Feather name="arrow-left" size={20} color="white" />
              </Pressable>
              <View style={{ backgroundColor: 'rgba(255,255,255,0.2)', borderRadius: 10, paddingHorizontal: 10, paddingVertical: 5 }}>
                <Text style={{ color: '#fff', fontSize: 11, fontWeight: '700', letterSpacing: 1 }}>HEALTH</Text>
              </View>
            </View>

            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 14 }}>
              <View style={{ backgroundColor: 'rgba(255,255,255,0.2)', padding: 14, borderRadius: 20 }}>
                <MaterialCommunityIcons name="heart-pulse" size={30} color="white" />
              </View>
              <View>
                <Text style={{ color: '#fff', fontSize: isTablet ? 28 : 22, fontWeight: '900' }}>Menstrual Health</Text>
                <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 13, marginTop: 2 }}>
                  Know your cycle, love your body
                </Text>
              </View>
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 48 }}>

        {/* Cycle phases */}
        <View style={{ paddingHorizontal: 20, marginTop: 24 }}>
          <Text style={{ fontSize: 18, fontWeight: '800', color: T.text, marginBottom: 4 }}>Cycle Phases</Text>
          <Text style={{ color: T.subtext, fontSize: 13, marginBottom: 16 }}>
            Tap a phase to learn more about it.
          </Text>

          <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 12 }}>
            {CYCLE_PHASES.map((phase, i) => {
              const isActive = activePhase === i;
              return (
                <Animated.View key={phase.name} entering={FadeInDown.delay(i * 80)} style={{ width: colW }}>
                  <Pressable
                    onPress={() => setActivePhase(isActive ? null : i)}
                    style={({ pressed }) => ({
                      backgroundColor: isDark ? (isActive ? `${phase.color}22` : '#1e293b') : (isActive ? phase.bg : '#fff'),
                      borderRadius: 22, padding: 18, alignItems: 'center',
                      borderWidth: isActive ? 2 : 1,
                      borderColor: isActive ? phase.color : T.border,
                      shadowColor: phase.color,
                      shadowOffset: { width: 0, height: 3 }, shadowOpacity: isActive ? 0.2 : 0.06,
                      shadowRadius: 8, elevation: isActive ? 4 : 2,
                      opacity: pressed ? 0.85 : 1,
                    })}
                  >
                    <View style={{
                      width: 48, height: 48, borderRadius: 16, marginBottom: 10,
                      backgroundColor: isDark ? `${phase.color}26` : phase.bg,
                      alignItems: 'center', justifyContent: 'center',
                    }}>
                      <MaterialCommunityIcons name={phase.icon as any} size={24} color={phase.color} />
                    </View>
                    <Text style={{ color: phase.color, fontWeight: '800', fontSize: 13, textAlign: 'center' }}>
                      {phase.name}
                    </Text>
                    <Text style={{ color: T.subtext, fontSize: 11, marginTop: 3 }}>Days {phase.days}</Text>
                    {isActive && (
                      <View style={{ marginTop: 10 }}>
                        <View style={{ width: 24, height: 2, borderRadius: 1, backgroundColor: phase.color, marginBottom: 8, alignSelf: 'center' }} />
                        <Text style={{ color: T.subtext, fontSize: 12, textAlign: 'center', lineHeight: 17 }}>
                          {phase.tip}
                        </Text>
                      </View>
                    )}
                  </Pressable>
                </Animated.View>
              );
            })}
          </View>
        </View>

        {/* Hygiene tips */}
        <View style={{ paddingHorizontal: 20, marginTop: 28 }}>
          <Text style={{ fontSize: 18, fontWeight: '800', color: T.text, marginBottom: 16 }}>Hygiene Tips</Text>
          <View style={{ gap: 10 }}>
            {HYGIENE_TIPS.map((tip, i) => (
              <Animated.View key={i} entering={FadeInDown.delay(300 + i * 60)}>
                <View style={{
                  backgroundColor: T.tipBg, borderRadius: 18, padding: 16,
                  flexDirection: 'row', alignItems: 'center', gap: 14,
                  borderWidth: 1, borderColor: T.border,
                  shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
                  shadowOpacity: isDark ? 0.2 : 0.05, shadowRadius: 5, elevation: 1,
                }}>
                  <View style={{
                    width: 40, height: 40, borderRadius: 12,
                    backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
                    alignItems: 'center', justifyContent: 'center',
                  }}>
                    <Ionicons name={tip.icon as any} size={20} color="#7c3aed" />
                  </View>
                  <Text style={{ color: T.text, fontSize: 13, flex: 1, lineHeight: 19 }}>{tip.text}</Text>
                </View>
              </Animated.View>
            ))}
          </View>
        </View>

        {/* See a doctor banner */}
        <Animated.View entering={FadeInDown.delay(700)} style={{ paddingHorizontal: 20, marginTop: 24 }}>
          <LinearGradient
            colors={isDark ? ['#1a1a2e', '#16213e'] : ['#f5f3ff', '#ede9fe']}
            style={{
              borderRadius: 22, padding: 20,
              borderWidth: 1, borderColor: isDark ? '#4c1d95' : '#ddd6fe',
              flexDirection: 'row', alignItems: 'center', gap: 14,
            }}
          >
            <View style={{
              backgroundColor: isDark ? '#2d1b69' : '#ede9fe',
              padding: 12, borderRadius: 16,
            }}>
              <Ionicons name="medkit" size={24} color="#7c3aed" />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={{ color: isDark ? '#a78bfa' : '#7c3aed', fontWeight: '800', fontSize: 14 }}>
                When to see a doctor
              </Text>
              <Text style={{ color: T.subtext, fontSize: 12, marginTop: 4, lineHeight: 18 }}>
                Seek medical attention for periods lasting more than 7 days, severe cramping, or irregular cycles.
              </Text>
            </View>
          </LinearGradient>
        </Animated.View>

      </ScrollView>
    </View>
  );
}
