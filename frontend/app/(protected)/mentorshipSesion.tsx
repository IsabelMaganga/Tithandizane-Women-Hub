import React, { Component } from 'react'
import {
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  Modal,
  StatusBar,
  ActivityIndicator,
  Alert,
  RefreshControl,
  Animated,
  Dimensions,
} from 'react-native'
import { SafeAreaView, SafeAreaProvider } from 'react-native-safe-area-context'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Ionicons from '@expo/vector-icons/Ionicons'


import {
  getMentorSessions,
  updateSessionStatus,
  startMentorshipConversation,
  MentorshipSession,
} from '../../services/api'

const { width: SCREEN_W } = Dimensions.get('window')

// ─── Tab config ──────────────────────────────────────────────────────────────

type TabKey = 'incoming' | 'missed' | 'past'

interface TabConfig {
  key: TabKey
  label: string
  icon: string
  activeColor: string
  activeBg: string
}

const TABS: TabConfig[] = [
  {
    key: 'incoming',
    label: 'Incoming',
    icon: 'download-outline',
    activeColor: '#4F6AF5',
    activeBg: '#EEF0FF',
  },
  {
    key: 'missed',
    label: 'Missed',
    icon: 'alert-circle-outline',
    activeColor: '#EF4444',
    activeBg: '#FEF2F2',
  },
  {
    key: 'past',
    label: 'Past',
    icon: 'archive-outline',
    activeColor: '#64748B',
    activeBg: '#F1F5F9',
  },
]

// ─── Helpers ─────────────────────────────────────────────────────────────────

const TOPIC_ACCENTS: [string, string][] = [
  ['career',      '#4F6AF5'],
  ['leadership',  '#8B5CF6'],
  ['marketing',   '#EC4899'],
  ['engineering', '#10B981'],
  ['health',      '#F59E0B'],
  ['education',   '#06B6D4'],
  ['menstrual',   '#E11D48'],
]
const TOPIC_ICONS: [string, string][] = [
  ['career',      'trending-up-outline'],
  ['leadership',  'people-outline'],
  ['marketing',   'megaphone-outline'],
  ['engineering', 'code-slash-outline'],
  ['health',      'heart-outline'],
  ['education',   'book-outline'],
  ['menstrual',   'medical-outline'],
]

const accentFor   = (t = '') => TOPIC_ACCENTS.find(([k]) => t.toLowerCase().includes(k))?.[1] ?? '#4F6AF5'
const iconFor     = (t = '') => TOPIC_ICONS.find(([k]) => t.toLowerCase().includes(k))?.[1]  ?? 'chatbubble-outline'
const initials    = (n = '') => n.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() ?? '').join('')
const fmtDate     = (d?: string | null) => d
  ? new Date(d).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
  : '—'
const fmtTime     = (t?: string | null) => {
  if (!t) return '—'
  const [h, m] = t.split(':').map(Number)
  return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${h >= 12 ? 'PM' : 'AM'}`
}
const duration    = (from?: string | null, to?: string | null) => {
  if (!from || !to) return ''
  const [fh, fm] = from.split(':').map(Number)
  const [th, tm] = to.split(':').map(Number)
  const mins = th * 60 + tm - (fh * 60 + fm)
  return mins > 0 ? `${mins} min` : ''
}
const isLive      = (s: MentorshipSession) => s.status === 'accepted' && !!s.conversation_started_at

// ─── Bucket logic ─────────────────────────────────────────────────────────────

function bucket(sessions: MentorshipSession[], tab: TabKey): MentorshipSession[] {
  switch (tab) {
    case 'incoming':
      // pending + accepted (live or not)
      return sessions.filter(s => s.status === 'pending' || s.status === 'accepted')
    case 'missed':
      return sessions.filter(s => s.status === 'missed' || s.is_missed)
    case 'past':
      return sessions.filter(s => s.status === 'completed' || s.status === 'declined')
  }
}

function countFor(sessions: MentorshipSession[], tab: TabKey): number {
  return bucket(sessions, tab).length
}

// ─── Cancel Modal ─────────────────────────────────────────────────────────────

interface CancelModalProps {
  visible: boolean
  session: MentorshipSession | null
  cancelling: boolean
  onConfirm: () => void
  onDismiss: () => void
}

const CancelModal: React.FC<CancelModalProps> = ({
  visible, session, cancelling, onConfirm, onDismiss,
}) => {
  if (!session) return null
  const accent = accentFor(session.topic)
  return (
    <Modal
      visible={visible}
      transparent
      animationType="fade"
      statusBarTranslucent
      onRequestClose={onDismiss}
    >
      <TouchableOpacity
        className="flex-1 bg-black/50 items-center justify-center px-6"
        activeOpacity={1}
        onPress={onDismiss}
      >
        <TouchableOpacity
          activeOpacity={1}
          className="w-full bg-white rounded-3xl overflow-hidden"
          onPress={() => {}}
        >
          <View className="h-1.5 w-full bg-red-500" />
          <View className="px-6 pt-6 pb-7">

            <View className="w-14 h-14 rounded-2xl bg-red-50 items-center justify-center mb-4">
              <Ionicons name="calendar-clear-outline" size={28} color="#EF4444" />
            </View>

            <Text className="text-[20px] font-extrabold text-slate-900 mb-1">
              Cancel this session?
            </Text>
            <Text className="text-[13px] text-slate-400 mb-5 leading-5">
              The session will be declined and the mentee will be notified.
            </Text>

            {/* Snapshot */}
            <View className="bg-slate-50 rounded-2xl p-4 mb-6">
              <View className="flex-row items-start gap-3 mb-3">
                <View className="w-1 rounded-full mt-0.5" style={{ backgroundColor: accent, height: 40 }} />
                <View className="flex-1">
                  <Text className="text-[13px] font-bold text-slate-800 leading-snug" numberOfLines={2}>
                    {session.topic}
                  </Text>
                  <Text className="text-[11px] text-slate-400 mt-0.5">
                    {session.mentee?.name ?? `Mentee #${session.mentee_id}`}
                  </Text>
                </View>
              </View>
              <View className="flex-row gap-2">
                <View className="flex-row items-center gap-1.5 bg-white px-2.5 py-1.5 rounded-lg">
                  <Ionicons name="calendar-outline" size={11} color="#64748B" />
                  <Text className="text-[11px] font-semibold text-slate-600">{fmtDate(session.requested_date)}</Text>
                </View>
                <View className="flex-row items-center gap-1.5 bg-white px-2.5 py-1.5 rounded-lg">
                  <Ionicons name="time-outline" size={11} color="#64748B" />
                  <Text className="text-[11px] font-semibold text-slate-600">{fmtTime(session.requested_time_from)}</Text>
                </View>
              </View>
            </View>

            <View className="flex-row gap-3">
              <TouchableOpacity
                className="flex-1 py-3.5 rounded-xl border border-slate-200 items-center justify-center"
                onPress={onDismiss}
                disabled={cancelling}
                activeOpacity={0.75}
              >
                <Text className="text-[14px] font-semibold text-slate-600">Keep It</Text>
              </TouchableOpacity>
              <TouchableOpacity
                className="flex-1 py-3.5 rounded-xl bg-red-500 items-center justify-center flex-row gap-2"
                onPress={onConfirm}
                disabled={cancelling}
                activeOpacity={0.8}
              >
                {cancelling
                  ? <ActivityIndicator size="small" color="#fff" />
                  : (
                    <>
                      <Ionicons name="trash-outline" size={15} color="#fff" />
                      <Text className="text-[14px] font-bold text-white">Yes, Cancel</Text>
                    </>
                  )}
              </TouchableOpacity>
            </View>

          </View>
        </TouchableOpacity>
      </TouchableOpacity>
    </Modal>
  )
}

// ─── Session Card ─────────────────────────────────────────────────────────────

interface CardProps {
  session: MentorshipSession
  startingId: number | null
  onStart: (s: MentorshipSession) => void
  onRequestCancel: (s: MentorshipSession) => void
}

const SessionCard: React.FC<CardProps> = ({ session, startingId, onStart, onRequestCancel }) => {
  const accent     = accentFor(session.topic)
  const live       = isLive(session)
  const menteeName = session.mentee?.name ?? `Mentee #${session.mentee_id}`
  const isStarting = startingId === session.id
  const canCancel  = session.status === 'pending' || session.status === 'accepted'
  const canStart   = session.status === 'accepted'

  // Status badge config
  const statusMap: Record<string, { label: string; icon: string; color: string; bg: string }> = {
    live:      { label: 'Live',      icon: 'radio-outline',            color: '#EF4444', bg: '#FEF2F2' },
    pending:   { label: 'Pending',   icon: 'hourglass-outline',        color: '#D97706', bg: '#FFFBEB' },
    accepted:  { label: 'Accepted',  icon: 'checkmark-circle-outline', color: '#10B981', bg: '#F0FDF4' },
    declined:  { label: 'Declined',  icon: 'close-circle-outline',     color: '#94A3B8', bg: '#F1F5F9' },
    completed: { label: 'Completed', icon: 'ribbon-outline',           color: '#4F6AF5', bg: '#EEF0FF' },
    missed:    { label: 'Missed',    icon: 'alert-circle-outline',     color: '#EF4444', bg: '#FEF2F2' },
  }
  const statusKey = live ? 'live' : session.status
  const badge = statusMap[statusKey] ?? statusMap['pending']

  return (
    <View
      className="bg-white rounded-2xl mb-4 flex-row overflow-hidden"
      style={{ elevation: live ? 6 : 3, shadowColor: '#0F1724', shadowOpacity: live ? 0.12 : 0.07, shadowRadius: 8, shadowOffset: { width: 0, height: 2 } }}
    >
      <View className="w-1.5" style={{ backgroundColor: accent }} />

      <View className="flex-1 p-4">

        {/* Badges row */}
        <View className="flex-row items-center gap-2 mb-2.5 flex-wrap">
          <View className="flex-row items-center px-2 py-1 rounded-md gap-1.5" style={{ backgroundColor: accent + '18' }}>
            <Ionicons name={iconFor(session.topic) as any} size={11} color={accent} />
            <Text className="text-xs font-bold" style={{ color: accent }}>{session.topic}</Text>
          </View>
          <View className="flex-row items-center px-2 py-1 rounded-md gap-1.5" style={{ backgroundColor: badge.bg }}>
            <Ionicons name={badge.icon as any} size={11} color={badge.color} />
            <Text className="text-xs font-bold" style={{ color: badge.color }}>{badge.label}</Text>
          </View>
        </View>

        {/* Title */}
        <Text className="text-[15px] font-bold text-slate-900 leading-snug mb-2">{session.topic}</Text>

        {/* Message preview */}
        {!!session.message && (
          <Text className="text-[12px] text-slate-400 mb-3 leading-5" numberOfLines={2}>{session.message}</Text>
        )}

        <View className="h-px bg-slate-100 mb-3" />

        {/* Mentee */}
        <View className="flex-row items-center gap-3 mb-3">
          <View className="w-10 h-10 rounded-full items-center justify-center" style={{ backgroundColor: accent + '22' }}>
            <Text className="text-sm font-extrabold" style={{ color: accent }}>{initials(menteeName)}</Text>
          </View>
          <View className="flex-1">
            <Text className="text-[11px] text-slate-400 font-medium mb-0.5">Mentee requesting</Text>
            <Text className="text-sm font-bold text-slate-800">{menteeName}</Text>
            {session.mentee?.email
              ? <Text className="text-[11px] text-slate-400">{session.mentee.email}</Text>
              : null}
          </View>
          <Ionicons name="person-circle-outline" size={22} color="#CBD5E1" />
        </View>

        {/* Schedule chips */}
        <View className="flex-row flex-wrap gap-1.5 mb-4">
          <View className="flex-row items-center bg-slate-50 px-2.5 py-1.5 rounded-lg gap-1.5">
            <Ionicons name="calendar-outline" size={12} color="#64748B" />
            <Text className="text-[11px] font-semibold text-slate-600">{fmtDate(session.requested_date)}</Text>
          </View>
          <View className="flex-row items-center bg-slate-50 px-2.5 py-1.5 rounded-lg gap-1.5">
            <Ionicons name="time-outline" size={12} color="#64748B" />
            <Text className="text-[11px] font-semibold text-slate-600">{fmtTime(session.requested_time_from)}</Text>
          </View>
          {!!duration(session.requested_time_from, session.requested_time_to) && (
            <View className="flex-row items-center bg-slate-50 px-2.5 py-1.5 rounded-lg gap-1.5">
              <Ionicons name="hourglass-outline" size={12} color="#64748B" />
              <Text className="text-[11px] font-semibold text-slate-600">
                {duration(session.requested_time_from, session.requested_time_to)}
              </Text>
            </View>
          )}
        </View>

        {/* Buttons */}
        <View className="flex-row gap-2.5">
          {canCancel && (
            <TouchableOpacity
              className="flex-1 py-3 rounded-xl border border-slate-200 items-center justify-center flex-row gap-1.5"
              onPress={() => onRequestCancel(session)}
              activeOpacity={0.7}
            >
              <Ionicons name="close-circle-outline" size={15} color="#94A3B8" />
              <Text className="text-[13px] font-semibold text-slate-500">Cancel</Text>
            </TouchableOpacity>
          )}
          {canStart && (
            <TouchableOpacity
              className="flex-[1.4] py-3 rounded-xl items-center justify-center flex-row gap-1.5"
              style={{ backgroundColor: accent }}
              onPress={() => onStart(session)}
              disabled={isStarting}
              activeOpacity={0.8}
            >
              {isStarting
                ? <ActivityIndicator size="small" color="#fff" />
                : (
                  <>
                    <Ionicons name={live ? 'enter-outline' : 'play-circle-outline'} size={15} color="#fff" />
                    <Text className="text-[13px] font-bold text-white">{live ? 'Rejoin' : 'Start Session'}</Text>
                  </>
                )}
            </TouchableOpacity>
          )}
          {!canCancel && !canStart && (
            <View className="flex-1 py-3 rounded-xl bg-slate-100 items-center justify-center">
              <Text className="text-[13px] font-semibold text-slate-400 capitalize">{session.status}</Text>
            </View>
          )}
        </View>
      </View>
    </View>
  )
}

// ─── Tab Bar ──────────────────────────────────────────────────────────────────

interface TabBarProps {
  active: TabKey
  sessions: MentorshipSession[]
  onChange: (tab: TabKey) => void
  indicatorAnim: Animated.Value
}

const TAB_W = (SCREEN_W - 32) / TABS.length

const TabBar: React.FC<TabBarProps> = ({ active, sessions, onChange, indicatorAnim }) => (
  <View className="mx-4 mb-4">
    {/* Pill container */}
    <View className="bg-slate-100 rounded-2xl p-1 flex-row">
      {/* Sliding indicator */}
      <Animated.View
        className="absolute top-1 bottom-1 rounded-xl"
        style={{
          width: TAB_W - 8,
          left: indicatorAnim,
          backgroundColor: '#fff',
          shadowColor: '#000',
          shadowOpacity: 0.08,
          shadowRadius: 4,
          shadowOffset: { width: 0, height: 1 },
          elevation: 2,
        }}
      />

      {TABS.map((tab, idx) => {
        const isActive = active === tab.key
        const count    = countFor(sessions, tab.key)

        return (
          <TouchableOpacity
            key={tab.key}
            className="flex-1 flex-row items-center justify-center gap-1.5 py-2.5"
            onPress={() => onChange(tab.key)}
            activeOpacity={0.8}
          >
            <Ionicons
              name={tab.icon as any}
              size={14}
              color={isActive ? tab.activeColor : '#94A3B8'}
            />
            <Text
              className="text-[12px] font-bold"
              style={{ color: isActive ? tab.activeColor : '#94A3B8' }}
            >
              {tab.label}
            </Text>
            {count > 0 && (
              <View
                className="rounded-full px-1.5 py-0.5 min-w-[18px] items-center"
                style={{ backgroundColor: isActive ? tab.activeColor : '#CBD5E1' }}
              >
                <Text className="text-[10px] font-extrabold text-white">{count}</Text>
              </View>
            )}
          </TouchableOpacity>
        )
      })}
    </View>
  </View>
)

// ─── Empty State ──────────────────────────────────────────────────────────────

const EMPTY_COPY: Record<TabKey, { icon: string; title: string; sub: string }> = {
  incoming: {
    icon: 'download-outline',
    title: 'No incoming sessions',
    sub: 'Mentee requests will appear here when they reach out.',
  },
  missed: {
    icon: 'alert-circle-outline',
    title: 'No missed sessions',
    sub: "You're all caught up — no sessions were missed.",
  },
  past: {
    icon: 'archive-outline',
    title: 'No past sessions',
    sub: 'Completed and declined sessions will show up here.',
  },
}

const EmptyTab: React.FC<{ tab: TabKey }> = ({ tab }) => {
  const { icon, title, sub } = EMPTY_COPY[tab]
  return (
    <View className="items-center py-20 gap-3 px-8">
      <View className="w-16 h-16 rounded-2xl bg-slate-100 items-center justify-center mb-2">
        <Ionicons name={icon as any} size={32} color="#CBD5E1" />
      </View>
      <Text className="text-[17px] font-bold text-slate-800 text-center">{title}</Text>
      <Text className="text-[13px] text-slate-400 text-center leading-5">{sub}</Text>
    </View>
  )
}

// ─── Screen ───────────────────────────────────────────────────────────────────

interface ScreenState {
  sessions: MentorshipSession[]
  loading: boolean
  refreshing: boolean
  activeTab: TabKey
  indicatorAnim: Animated.Value
  cancelTarget: MentorshipSession | null
  modalVisible: boolean
  cancelling: boolean
  startingId: number | null
  token: string | null
}

export default class MentorshipSessionScreen extends Component<{}, ScreenState> {
  state: ScreenState = {
    sessions: [],
    loading: true,
    refreshing: false,
    activeTab: 'incoming',
    indicatorAnim: new Animated.Value(4), // initial x for first tab
    cancelTarget: null,
    modalVisible: false,
    cancelling: false,
    startingId: null,
    token: null,
  }

  async componentDidMount() {
    const token = await AsyncStorage.getItem('token')
    this.setState({ token }, this.loadSessions)
  }

  // ── Data ───────────────────────────────────────────────────────────────────

  loadSessions = async (silent = false) => {
    const { token } = this.state
    if (!token) { this.setState({ loading: false }); return }
    if (!silent) this.setState({ loading: true })
    try {
      const sessions = await getMentorSessions(token)
      this.setState({ sessions, loading: false, refreshing: false })
    } catch (e: any) {
      this.setState({ loading: false, refreshing: false })
      Alert.alert('Error', e?.message ?? 'Could not load sessions.')
    }
  }

  onRefresh = () => {
    this.setState({ refreshing: true })
    this.loadSessions(true)
  }

  // ── Tab switching ──────────────────────────────────────────────────────────

  switchTab = (tab: TabKey) => {
    const idx = TABS.findIndex(t => t.key === tab)
    const toX = 4 + idx * TAB_W  // 4px left padding of pill container
    Animated.spring(this.state.indicatorAnim, {
      toValue: toX,
      useNativeDriver: false,
      tension: 60,
      friction: 10,
    }).start()
    this.setState({ activeTab: tab })
  }

  // ── Start ──────────────────────────────────────────────────────────────────

  handleStart = async (session: MentorshipSession) => {
    const { token } = this.state
    if (!token) return
    this.setState({ startingId: session.id })
    try {
      const res = await startMentorshipConversation(session.id, token)
      if (res.conversation) {
        // navigation.navigate('Chat', { conversationId: res.conversation.id })
        Alert.alert('Session Started', `Conversation #${res.conversation.id} is ready.`)
        this.loadSessions(true)
      } else {
        Alert.alert('Notice', res.message ?? 'Could not start session.')
      }
    } catch (e: any) {
      Alert.alert('Cannot Start', e?.message ?? 'Session unavailable.')
    } finally {
      this.setState({ startingId: null })
    }
  }

  // ── Cancel ─────────────────────────────────────────────────────────────────

  handleRequestCancel = (session: MentorshipSession) =>
    this.setState({ cancelTarget: session, modalVisible: true })

  handleDismissModal = () =>
    this.setState({ cancelTarget: null, modalVisible: false })

  handleConfirmCancel = async () => {
    const { cancelTarget, token } = this.state
    if (!cancelTarget || !token) return
    this.setState({ cancelling: true })
    try {
      await updateSessionStatus(cancelTarget.id, token, { status: 'declined' })
      this.setState(prev => ({
        sessions: prev.sessions.map(s =>
          s.id === cancelTarget.id ? { ...s, status: 'declined' as const } : s
        ),
        cancelTarget: null,
        modalVisible: false,
        cancelling: false,
      }))
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not cancel session.')
      this.setState({ cancelling: false })
    }
  }

  // ── Render ─────────────────────────────────────────────────────────────────

  render() {
    const {
      sessions, loading, refreshing,
      activeTab, indicatorAnim,
      cancelTarget, modalVisible, cancelling, startingId,
    } = this.state

    const visibleSessions = bucket(sessions, activeTab)
    const incomingCount   = countFor(sessions, 'incoming')

    const cardProps = {
      startingId,
      onStart: this.handleStart,
      onRequestCancel: this.handleRequestCancel,
    }

    return (
      <SafeAreaProvider>
        <SafeAreaView className="flex-1 bg-slate-50" edges={['top', 'left', 'right']}>
          <StatusBar barStyle="dark-content" backgroundColor="#F8FAFC" />

          {/* ── Header ── */}
          <View className="flex-row justify-between items-center px-5 pt-4 pb-5">
            <View>
              <Text className="text-[11px] font-semibold tracking-widest text-slate-400 uppercase mb-0.5">
                Mentor Dashboard
              </Text>
              <Text className="text-[26px] font-extrabold text-slate-900 -tracking-[0.5px]">
                Your Sessions
              </Text>
            </View>

            {/* Live indicator badge — only if something is active */}
            {sessions.some(s => isLive(s)) && (
              <View className="flex-row items-center bg-red-50 border border-red-100 rounded-xl px-3 py-2 gap-1.5">
                <View className="w-2 h-2 rounded-full bg-red-500" />
                <Text className="text-[12px] font-bold text-red-500">Live</Text>
              </View>
            )}

            {/* Session count — only if no live */}
            {!sessions.some(s => isLive(s)) && (
              <View className="bg-indigo-500 rounded-xl px-3.5 py-2 items-center">
                <Text className="text-xl font-extrabold text-white leading-6">{sessions.length}</Text>
                <Text className="text-[10px] font-semibold text-indigo-200 uppercase tracking-widest">total</Text>
              </View>
            )}
          </View>

          {/* ── Tab Bar (always visible, even during load) ── */}
          <TabBar
            active={activeTab}
            sessions={sessions}
            onChange={this.switchTab}
            indicatorAnim={indicatorAnim}
          />

          {/* ── Body ── */}
          {loading ? (
            <View className="flex-1 items-center justify-center gap-3">
              <ActivityIndicator size="large" color="#4F6AF5" />
              <Text className="text-slate-400 text-sm">Loading sessions…</Text>
            </View>
          ) : (
            <ScrollView
              className="flex-1"
              contentContainerStyle={{ paddingHorizontal: 16, paddingBottom: 40 }}
              showsVerticalScrollIndicator={false}
              refreshControl={
                <RefreshControl
                  refreshing={refreshing}
                  onRefresh={this.onRefresh}
                  tintColor="#4F6AF5"
                  colors={['#4F6AF5']}
                />
              }
            >
              {/* Live sub-header inside Incoming tab */}
              {activeTab === 'incoming' && sessions.some(s => isLive(s)) && (
                <View className="flex-row items-center gap-2 mb-3 ml-1">
                  <View className="w-2 h-2 rounded-full bg-red-500" />
                  <Text className="text-[11px] font-bold text-red-400 uppercase tracking-widest">
                    Active Now
                  </Text>
                </View>
              )}

              {visibleSessions.length === 0
                ? <EmptyTab tab={activeTab} />
                : visibleSessions.map(s => (
                    <SessionCard key={s.id} session={s} {...cardProps} />
                  ))
              }
            </ScrollView>
          )}

          {/* ── Cancel Modal ── */}
          <CancelModal
            visible={modalVisible}
            session={cancelTarget}
            cancelling={cancelling}
            onConfirm={this.handleConfirmCancel}
            onDismiss={this.handleDismissModal}
          />

        </SafeAreaView>
      </SafeAreaProvider>
    )
  }
}