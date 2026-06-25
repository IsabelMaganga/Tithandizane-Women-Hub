import React, { useEffect, useState, useRef } from "react";
import {
  View,
  Text,
  TextInput,
  Pressable,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Modal,
  TouchableWithoutFeedback,
  Alert,
  ScrollView,
} from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { Feather, Ionicons } from "@expo/vector-icons";
import {
  getMessages,
  sendMessage,
  getConversation,
  createConversation,
  terminateMentorshipSession,
  submitSessionReview,
} from "@/services/api";
import { useAuth } from "@/context/AuthContext";
import { subscribeToChatChannel } from "@/services/echo";
import { StatusBar } from "expo-status-bar";
import { SafeAreaView } from "react-native-safe-area-context";

type MessageStatus = "sending" | "sent" | "delivered" | "read";

interface Message {
  id: number;
  sender_id: number;
  message: string;
  sender?: { name: string };
  created_at?: string;
  status?: MessageStatus;
  read_at?: string | null;
}

interface Conversation {
  id: number;
  name?: string | null;
  is_group?: boolean;
  session?: { status?: string } | null;
}

// ─── Read Receipt ─────────────────────────────────────────────────────────────

const ReadReceipt = ({ status }: { status: MessageStatus }) => {
  if (status === "sending") {
    return <Text style={{ color: "#94A3B8", fontSize: 10, marginLeft: 2 }}>●</Text>;
  }
  if (status === "sent") {
    return (
      <View style={{ flexDirection: "row", marginLeft: 2 }}>
        <Ionicons name="checkmark" size={12} color="#CBD5E1" />
      </View>
    );
  }
  if (status === "delivered") {
    return (
      <View style={{ flexDirection: "row", marginLeft: 2 }}>
        <Ionicons name="checkmark" size={12} color="#CBD5E1" style={{ marginRight: -5 }} />
        <Ionicons name="checkmark" size={12} color="#CBD5E1" />
      </View>
    );
  }
  if (status === "read") {
    return (
      <View style={{ flexDirection: "row", marginLeft: 2 }}>
        <Ionicons name="checkmark" size={12} color="#3B82F6" style={{ marginRight: -5 }} />
        <Ionicons name="checkmark" size={12} color="#3B82F6" />
      </View>
    );
  }
  return null;
};

// ─── Heart Rating ─────────────────────────────────────────────────────────────

const HeartRating = ({
  rating,
  onChange,
}: {
  rating: number;
  onChange: (val: number) => void;
}) => (
  <View style={{ flexDirection: "row", justifyContent: "center", gap: 8, marginTop: 4 }}>
    {[1, 2, 3, 4, 5].map((val) => (
      <Pressable
        key={val}
        onPress={() => onChange(val)}
        style={({ pressed }) => ({
          opacity: pressed ? 0.7 : 1,
          transform: [{ scale: pressed ? 0.9 : 1 }],
        })}
      >
        <Ionicons
          name={rating >= val ? "heart" : "heart-outline"}
          size={36}
          color={rating >= val ? "#7C3AED" : "#CBD5E1"}
        />
      </Pressable>
    ))}
  </View>
);

// ─── Review Modal ─────────────────────────────────────────────────────────────

const ReviewModal = ({
  visible,
  mentorName,
  onClose,
  onSubmit,
  submitting,
}: {
  visible: boolean;
  mentorName: string;
  onClose: () => void;
  onSubmit: (rating: number, comment: string) => void;
  submitting: boolean;
}) => {
  const [rating, setRating] = useState(0);
  const [comment, setComment] = useState("");
  const ratingLabels = ["", "Poor", "Fair", "Good", "Great", "Excellent"];

  const handleSubmit = () => {
    if (rating === 0) {
      Alert.alert("Rating required", "Please select a rating before submitting.");
      return;
    }
    onSubmit(rating, comment);
  };

  const handleClose = () => {
    setRating(0);
    setComment("");
    onClose();
  };

  return (
    <Modal
      visible={visible}
      transparent
      animationType="fade"
      statusBarTranslucent
      onRequestClose={handleClose}
    >
      <TouchableWithoutFeedback onPress={handleClose}>
        <View style={{ flex: 1, backgroundColor: "rgba(15, 10, 30, 0.6)", justifyContent: "flex-end" }}>
          <TouchableWithoutFeedback>
            <View
              style={{
                backgroundColor: "#FFFFFF",
                borderTopLeftRadius: 28,
                borderTopRightRadius: 28,
                paddingHorizontal: 24,
                paddingTop: 12,
                paddingBottom: 40,
              }}
            >
              <View style={{ width: 40, height: 4, backgroundColor: "#E2E8F0", borderRadius: 2, alignSelf: "center", marginBottom: 24 }} />
              <View style={{ alignItems: "center", marginBottom: 24 }}>
                <View style={{ width: 64, height: 64, borderRadius: 32, backgroundColor: "#EDE9FE", alignItems: "center", justifyContent: "center", marginBottom: 14, borderWidth: 2, borderColor: "#DDD6FE" }}>
                  <Text style={{ fontSize: 26, fontWeight: "700", color: "#7C3AED" }}>
                    {mentorName?.charAt(0)?.toUpperCase() ?? "M"}
                  </Text>
                </View>
                <Text style={{ fontSize: 20, fontWeight: "700", color: "#0F172A", marginBottom: 4 }}>How was your session?</Text>
                <Text style={{ fontSize: 14, color: "#64748B", textAlign: "center" }}>
                  Share your experience with <Text style={{ fontWeight: "600", color: "#4C1D95" }}>{mentorName}</Text>
                </Text>
              </View>
              <View style={{ height: 1, backgroundColor: "#F1F5F9", marginBottom: 24 }} />
              <View style={{ marginBottom: 24 }}>
                <Text style={{ fontSize: 13, fontWeight: "600", color: "#475569", textTransform: "uppercase", letterSpacing: 0.8, textAlign: "center", marginBottom: 14 }}>
                  Rate your mentor
                </Text>
                <HeartRating rating={rating} onChange={setRating} />
                {rating > 0 && (
                  <Text style={{ textAlign: "center", marginTop: 10, fontSize: 14, fontWeight: "600", color: "#7C3AED" }}>
                    {ratingLabels[rating]}
                  </Text>
                )}
              </View>
              <View style={{ marginBottom: 28 }}>
                <Text style={{ fontSize: 13, fontWeight: "600", color: "#475569", textTransform: "uppercase", letterSpacing: 0.8, marginBottom: 10 }}>
                  Leave a note <Text style={{ color: "#94A3B8", fontWeight: "400", textTransform: "none", letterSpacing: 0 }}>(optional)</Text>
                </Text>
                <TextInput
                  value={comment}
                  onChangeText={setComment}
                  placeholder={`What did ${mentorName} do well? Any suggestions?`}
                  placeholderTextColor="#94A3B8"
                  multiline
                  numberOfLines={4}
                  maxLength={500}
                  textAlignVertical="top"
                  style={{ backgroundColor: "#F8FAFC", borderWidth: 1.5, borderColor: comment.length > 0 ? "#A78BFA" : "#E2E8F0", borderRadius: 16, padding: 14, fontSize: 15, color: "#1E293B", minHeight: 110, lineHeight: 22 }}
                />
                <Text style={{ textAlign: "right", marginTop: 6, fontSize: 11, color: "#94A3B8" }}>{comment.length}/500</Text>
              </View>
              <View style={{ gap: 10 }}>
                <Pressable
                  onPress={handleSubmit}
                  disabled={submitting || rating === 0}
                  style={({ pressed }) => ({ backgroundColor: rating === 0 ? "#E2E8F0" : pressed ? "#6D28D9" : "#7C3AED", borderRadius: 16, paddingVertical: 16, alignItems: "center", justifyContent: "center" })}
                >
                  {submitting ? (
                    <ActivityIndicator size="small" color="#fff" />
                  ) : (
                    <Text style={{ fontSize: 16, fontWeight: "700", color: rating === 0 ? "#94A3B8" : "#FFFFFF", letterSpacing: 0.2 }}>Submit Review</Text>
                  )}
                </Pressable>
                <Pressable onPress={handleClose} style={({ pressed }) => ({ borderRadius: 16, paddingVertical: 14, alignItems: "center", opacity: pressed ? 0.5 : 1 })}>
                  <Text style={{ fontSize: 15, color: "#94A3B8", fontWeight: "500" }}>Skip for now</Text>
                </Pressable>
              </View>
            </View>
          </TouchableWithoutFeedback>
        </View>
      </TouchableWithoutFeedback>
    </Modal>
  );
};

// ─── Terminate Confirm Modal ──────────────────────────────────────────────────

const TerminateConfirmModal = ({
  visible, onCancel, onConfirm, terminating,
}: {
  visible: boolean; onCancel: () => void; onConfirm: () => void; terminating: boolean;
}) => (
  <Modal
    visible={visible}
    transparent={true}
    animationType="fade"
    statusBarTranslucent={true}
  >
    <View
      style={{
        flex: 1,
        backgroundColor: "rgba(0,0,0,0.5)",
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 24,
      }}
    >
      <View
        style={{
          width: "100%",
          backgroundColor: "#FFFFFF",
          borderRadius: 20,
          padding: 24,
        }}
      >
        <View
          style={{
            width: 56,
            height: 56,
            borderRadius: 28,
            backgroundColor: "#FEF2F2",
            alignItems: "center",
            justifyContent: "center",
            alignSelf: "center",
            marginBottom: 16,
          }}
        >
          <Ionicons name="stop-circle" size={28} color="#EF4444" />
        </View>

        <Text
          style={{
            fontSize: 18,
            fontWeight: "700",
            color: "#0F172A",
            textAlign: "center",
            marginBottom: 8,
          }}
        >
          End this session?
        </Text>

        <Text
          style={{
            fontSize: 14,
            color: "#64748B",
            textAlign: "center",
            lineHeight: 20,
            marginBottom: 24,
          }}
        >
          This will close the session permanently. The mentee will be asked to leave a review.
        </Text>

        <Pressable
          onPress={() => {
            console.log("End Session pressed");
            onConfirm();
          }}
          disabled={terminating}
          style={{
            borderRadius: 14,
            paddingVertical: 16,
            alignItems: "center",
            backgroundColor: "#EF4444",
            marginBottom: 10,
          }}
        >
          {terminating ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Text style={{ fontSize: 15, fontWeight: "700", color: "#FFFFFF" }}>
              End Session
            </Text>
          )}
        </Pressable>

        <Pressable
          onPress={() => {
            console.log("Keep Going pressed");
            onCancel();
          }}
          style={{
            borderRadius: 14,
            paddingVertical: 16,
            alignItems: "center",
            backgroundColor: "#22C55E",
          }}
        >
          <Text style={{ fontSize: 15, fontWeight: "700", color: "#FFFFFF" }}>
            Keep Going
          </Text>
        </Pressable>
      </View>
    </View>
  </Modal>
);

// ─── Message Bubble ───────────────────────────────────────────────────────────

const MessageBubble = ({ item, isMe }: { item: Message; isMe: boolean }) => {
  const status = item.status ?? (isMe ? "sent" : undefined);
  return (
    <View style={{ marginBottom: 12, maxWidth: "85%", alignSelf: isMe ? "flex-end" : "flex-start" }}>
      <View
        style={{
          paddingHorizontal: 16, paddingVertical: 12, borderRadius: 24,
          ...(isMe
            ? { backgroundColor: "#9333EA", borderTopRightRadius: 4, shadowColor: "#9333EA", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, elevation: 3 }
            : { backgroundColor: "#FFFFFF", borderTopLeftRadius: 4, borderWidth: 1, borderColor: "#F1F5F9", shadowColor: "#000", shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 2, elevation: 1 }),
        }}
      >
        <Text style={{ fontSize: 15, lineHeight: 20, color: isMe ? "#FFFFFF" : "#1E293B" }}>
          {item.message}
        </Text>
      </View>
      <View style={{ flexDirection: "row", alignItems: "center", marginTop: 2, paddingHorizontal: 4, justifyContent: isMe ? "flex-end" : "flex-start" }}>
        {!isMe && <Text style={{ fontSize: 10, color: "#94A3B8" }}>{item.sender?.name ?? ""}</Text>}
        {isMe && status && (
          <View style={{ flexDirection: "row", alignItems: "center" }}>
            <Text style={{ fontSize: 10, color: "#94A3B8", marginRight: 2 }}>
              {status === "sending" ? "Sending…" : status === "sent" ? "Sent" : status === "delivered" ? "Delivered" : "Read"}
            </Text>
            <ReadReceipt status={status} />
          </View>
        )}
      </View>
    </View>
  );
};

// ─── Session Ended Footer ─────────────────────────────────────────────────────

const SessionEndedFooter = ({ onBookNew }: { onBookNew: () => void }) => (
  <View
    style={{
      backgroundColor: "#FFFFFF",
      borderTopWidth: 1,
      borderTopColor: "#F1F5F9",
      paddingHorizontal: 20,
      paddingTop: 24,
      paddingBottom: 40,
      alignItems: "center",
    }}
  >
    <View
      style={{
        width: 52,
        height: 52,
        borderRadius: 26,
        backgroundColor: "#F1F5F9",
        alignItems: "center",
        justifyContent: "center",
        marginBottom: 12,
      }}
    >
      <Ionicons name="lock-closed" size={22} color="#94A3B8" />
    </View>

    <Text style={{ fontSize: 15, fontWeight: "700", color: "#475569", marginBottom: 4 }}>
      Session Ended
    </Text>
    <Text style={{ fontSize: 13, color: "#94A3B8", textAlign: "center", marginBottom: 24 }}>
      This conversation is now closed. Start a new session to continue.
    </Text>

    <Pressable
      onPress={onBookNew}
      style={({ pressed }) => ({
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: pressed ? "#7C3AED" : "#9333EA",
        borderRadius: 14,
        paddingVertical: 14,
        paddingHorizontal: 28,
      })}
    >
      <Ionicons name="calendar-outline" size={17} color="#FFFFFF" />
      <Text style={{ fontSize: 15, fontWeight: "700", color: "#FFFFFF" }}>
        Book New Session
      </Text>
    </Pressable>
  </View>
);

// ─── Chat Screen ──────────────────────────────────────────────────────────────

const ENDED_STATUSES = ["completed", "missed", "declined", "cancelled"];

const ChatScreen = () => {
  const params = useLocalSearchParams<{
    id: string;
    isNew?: string;
    name?: string;
    sessionId?: string;
    isMentor?: string;
    sessionStatus?: string; // ← NEW: pass current session status when navigating here
  }>();

  const id               = Array.isArray(params.id)            ? params.id[0]            : params.id;
  const isNew            = Array.isArray(params.isNew)          ? params.isNew[0]         : params.isNew;
  const name             = Array.isArray(params.name)           ? params.name[0]          : params.name;
  const sessionId        = Array.isArray(params.sessionId)      ? params.sessionId[0]     : params.sessionId;
  const isMentorParam    = Array.isArray(params.isMentor)       ? params.isMentor[0]      : params.isMentor;
  const sessionStatusParam = Array.isArray(params.sessionStatus) ? params.sessionStatus[0] : params.sessionStatus; // ← NEW

  const { user, token } = useAuth();
  const router = useRouter();

  const [activeId, setActiveId]                         = useState<number | null>(isNew === "true" ? null : Number(id));
  const [messages, setMessages]                         = useState<Message[]>([]);
  const [conversation, setConversation]                 = useState<Conversation | null>(null);
  const [text, setText]                                 = useState("");
  const [loading, setLoading]                           = useState(true);
  const [sending, setSending]                           = useState(false);
  const [typingUser, setTypingUser]                     = useState<string | null>(null);
  const [showTerminateConfirm, setShowTerminateConfirm] = useState(false);
  const [terminating, setTerminating]                   = useState(false);
  const [showReviewModal, setShowReviewModal]           = useState(false);
  const [submittingReview, setSubmittingReview]         = useState(false);

  // ── Initialize sessionEnded from the nav param so it's correct on first render ──
  const [sessionEnded, setSessionEnded] = useState(
    () => ENDED_STATUSES.includes(sessionStatusParam ?? "")
  );

  const scrollRef = useRef<ScrollView>(null);

  const scrollToBottom = (animated = true) => {
    scrollRef.current?.scrollToEnd({ animated });
  };

  const activeSessionId: number | null = (() => {
    if (!sessionId || sessionId === "undefined" || sessionId === "null") return null;
    const n = Number(sessionId);
    return isNaN(n) || n === 0 ? null : n;
  })();

  const isMentor      = user?.role === "mentor" || isMentorParam === "true";
  const showEndButton = isMentor && activeSessionId !== null && !sessionEnded;

  const unsubscribeRef   = useRef<(() => void) | null>(null);
  const optimisticIdsRef = useRef<Set<number>>(new Set());
  const confirmedIdsRef  = useRef<Set<number>>(new Set());
  const isFirstLoad      = useRef(true);

  const deduplicateMessages = (arr: Message[]): Message[] =>
    Array.from(new Map(arr.map((m) => [m.id, m])).values());

  const deriveStatus = (msg: Message): MessageStatus => {
    if (msg.read_at)    return "read";
    if (msg.created_at) return "delivered";
    return "sent";
  };

  const markMessagesAsRead = async (_convId: number) => {
    try {
      setMessages((prev) =>
        prev.map((m) =>
          m.sender_id !== user?.id && !m.read_at
            ? { ...m, read_at: new Date().toISOString(), status: "read" }
            : m
        )
      );
    } catch { /* silently fail */ }
  };

  useEffect(() => {
    if (messages.length === 0) return;
    scrollToBottom(!isFirstLoad.current);
    isFirstLoad.current = false;
  }, [messages.length]);

  // ── Terminate session ────────────────────────────────────────────────────
  const handleTerminateConfirmed = async () => {
    if (!activeSessionId) {
      Alert.alert("Error", "Missing session ID.");
      return;
    }

    setTerminating(true);

    try {
      const data = await terminateMentorshipSession(activeSessionId, token!);

      console.log("Terminate success:", data);

      setSessionEnded(true);
      setShowTerminateConfirm(false);

      if (!isMentor) {
        setTimeout(() => setShowReviewModal(true), 400);
      }
    } catch (error: any) {
      console.log("Terminate error:", error.response?.data ?? error);

      Alert.alert(
        "Error",
        error.response?.data?.message ?? error.message ?? "Failed to terminate session."
      );
    } finally {
      setTerminating(false);
    }
  };

  const handleSubmitReview = async (rating: number, comment: string) => {
    if (!activeSessionId || !token) {
      setShowReviewModal(false);
      router.back();
      return;
    }
    setSubmittingReview(true);
    try {
      await submitSessionReview(activeSessionId, token, {
        rating,
        comment: comment.trim() || undefined,
      });
      setShowReviewModal(false);
      setTimeout(() => router.back(), 300);
    } catch (err: any) {
      Alert.alert("Error", err?.message ?? "Could not submit your review. Please try again.");
    } finally {
      setSubmittingReview(false);
    }
  };

  const setupWebsocket = (convId: number, wsToken: string) => {
    if (unsubscribeRef.current) {
      unsubscribeRef.current();
      unsubscribeRef.current = null;
    }
    unsubscribeRef.current = subscribeToChatChannel(wsToken, convId, (e: any) => {
      const incoming: Message = { ...e.message, status: deriveStatus(e.message) };
      if (confirmedIdsRef.current.has(incoming.id)) return;
      confirmedIdsRef.current.add(incoming.id);

      setMessages((prev) => {
        const optimisticIndex = prev.findIndex(
          (m) =>
            optimisticIdsRef.current.has(m.id) &&
            m.sender_id === incoming.sender_id &&
            m.message === incoming.message
        );
        if (optimisticIndex !== -1) {
          optimisticIdsRef.current.delete(prev[optimisticIndex].id);
          const updated = [...prev];
          updated[optimisticIndex] = incoming;
          return updated;
        }
        if (prev.some((m) => m.id === incoming.id)) return prev;
        const finalMsg =
          incoming.sender_id !== user?.id
            ? { ...incoming, read_at: new Date().toISOString(), status: "read" as MessageStatus }
            : incoming;
        return [...prev, finalMsg];
      });
    });
  };

  useEffect(() => {
    return () => {
      if (unsubscribeRef.current) unsubscribeRef.current();
    };
  }, []);

  useEffect(() => {
    let isMounted = true;
    const initChat = async () => {
      if (!token) return;
      try {
        if (isNew === "true" && !activeId) {
          if (isMounted) {
            setConversation({ id: 0, name: name ?? "", is_group: false });
            setLoading(false);
          }
          return;
        }
        const targetId = activeId || Number(id);
        const [msgs, convo] = await Promise.all([
          getMessages(targetId, token),
          getConversation(targetId, token),
        ]);
        if (!isMounted) return;

        // ── Sync sessionEnded from API response (safety net for deep links / refreshes) ──
        if (convo?.session?.status && ENDED_STATUSES.includes(convo.session.status)) {
          setSessionEnded(true);
        }

        const withStatus: Message[] = msgs.map((m: Message) => ({
          ...m,
          status: m.sender_id === user?.id ? deriveStatus(m) : undefined,
        }));
        const deduped = deduplicateMessages(withStatus);
        deduped.forEach((m) => confirmedIdsRef.current.add(m.id));
        setMessages(deduped);
        setConversation(convo);
        setupWebsocket(targetId, token);
        markMessagesAsRead(targetId);
      } catch (err) {
        console.error("Init chat error:", err);
      } finally {
        if (isMounted) setLoading(false);
      }
    };
    initChat();
    return () => { isMounted = false; };
  }, [id, activeId, token]);

  const handleSend = async () => {
    const messageText = text.trim();
    if (!messageText || sending || sessionEnded || !token) return;
    setSending(true);
    setText("");

    const optimisticId = -Date.now();
    optimisticIdsRef.current.add(optimisticId);
    const optimisticMsg: Message = {
      id: optimisticId,
      sender_id: user?.id ?? 0,
      message: messageText,
      status: "sending",
    };
    setMessages((prev) => [...prev, optimisticMsg]);

    try {
      let currentConvId = activeId;
      if (!currentConvId) {
        const newConvo = await createConversation({ receiver_id: Number(id) });
        currentConvId = newConvo.id;
        setConversation(newConvo);
        setActiveId(currentConvId);
      }
      const confirmed = await sendMessage(currentConvId!, messageText, token);
      confirmedIdsRef.current.add(confirmed.id);
      setMessages((prev) => {
        const updated = prev.map((m) =>
          m.id === optimisticId ? { ...confirmed, status: "sent" as MessageStatus } : m
        );
        return deduplicateMessages(updated);
      });
      optimisticIdsRef.current.delete(optimisticId);
    } catch (err) {
      console.error("Send message error:", err);
      setMessages((prev) => prev.filter((m) => m.id !== optimisticId));
      optimisticIdsRef.current.delete(optimisticId);
      setText(messageText);
    } finally {
      setSending(false);
    }
  };

  const getTitle = (): string => {
    if (!conversation) return name ?? "";
    const conversationName = conversation.name ?? name ?? "";
    return conversation.is_group ? conversationName : conversationName;
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#8A4FFF" />
      </View>
    );
  }

  return (
    <View className="flex-1 bg-[#F8FAFC]">
      <StatusBar style="dark" />

      {/* ── HEADER ─────────────────────────────────────────────────────────── */}
      <View className="pt-14 pb-3 px-4 bg-white border-b border-slate-100 flex-row items-center shadow-sm">
        <Pressable onPress={() => router.back()} className="pr-3 active:opacity-50">
          <Feather name="chevron-left" size={28} color="#1E293B" />
        </Pressable>

        <View className="w-10 h-10 rounded-full bg-purple-100 items-center justify-center">
          <Text className="text-purple-600 font-bold">
            {getTitle()?.charAt(0)?.toUpperCase()}
          </Text>
        </View>

        <View className="ml-3 flex-1">
          <Text className="text-slate-900 font-bold text-base" numberOfLines={1}>
            {getTitle()}
          </Text>
          <Text className="text-slate-400 text-[11px] font-medium">
            {sessionEnded
              ? "Session ended"
              : typingUser
              ? `${typingUser} is typing...`
              : "Online"}
          </Text>
        </View>

        {/* End button — visible to mentor only while session is active */}
        {showEndButton && (
          <Pressable
            onPress={() => setShowTerminateConfirm(true)}
            style={({ pressed }) => ({
              flexDirection: "row",
              alignItems: "center",
              gap: 5,
              backgroundColor: pressed ? "#FEE2E2" : "#FEF2F2",
              paddingHorizontal: 12,
              paddingVertical: 7,
              borderRadius: 20,
              borderWidth: 1,
              borderColor: "#FECACA",
              marginLeft: 8,
            })}
          >
            <Ionicons name="stop-circle" size={15} color="#EF4444" />
            <Text style={{ fontSize: 12, fontWeight: "700", color: "#EF4444", letterSpacing: 0.2 }}>
              End
            </Text>
          </Pressable>
        )}

        {/* Closed badge */}
        {sessionEnded && (
          <View
            style={{
              flexDirection: "row",
              alignItems: "center",
              gap: 4,
              backgroundColor: "#F1F5F9",
              paddingHorizontal: 10,
              paddingVertical: 6,
              borderRadius: 20,
              marginLeft: 8,
            }}
          >
            <Ionicons name="lock-closed" size={12} color="#94A3B8" />
            <Text style={{ fontSize: 11, fontWeight: "600", color: "#94A3B8" }}>Closed</Text>
          </View>
        )}
      </View>

      {/* ── MESSAGES + INPUT ───────────────────────────────────────────────── */}
      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === "ios" ? "padding" : "height"}
      >
        <ScrollView
          ref={scrollRef}
          style={{ flex: 1 }}
          contentContainerStyle={{ paddingHorizontal: 16, paddingTop: 16, paddingBottom: 12 }}
          showsVerticalScrollIndicator={false}
          keyboardDismissMode="interactive"
          keyboardShouldPersistTaps="handled"
          onContentSizeChange={() => scrollToBottom(false)}
        >
          {messages.length === 0 ? (
            <View style={{ alignItems: "center", marginTop: 40, paddingHorizontal: 40 }}>
              <Text style={{ color: "#CBD5E1", textAlign: "center", fontWeight: "500" }}>
                Your conversation starts here. Messages are encrypted and private.
              </Text>
            </View>
          ) : (
            messages.map((item) => (
              <MessageBubble
                key={String(item.id)}
                item={item}
                isMe={item.sender_id === user?.id}
              />
            ))
          )}
        </ScrollView>

        {/* ── INPUT BAR or SESSION ENDED FOOTER ─────────────────────────── */}
        {sessionEnded ? (
          <SessionEndedFooter onBookNew={() => router.back()} />
        ) : (
          <SafeAreaView
            edges={["bottom"]}
            style={{ backgroundColor: "#FFFFFF", borderTopWidth: 1, borderTopColor: "#F1F5F9" }}
          >
            <View style={{ padding: 12, flexDirection: "row", alignItems: "flex-end", gap: 8 }}>
              <View
                style={{
                  flex: 1,
                  borderRadius: 24,
                  paddingHorizontal: 16,
                  paddingVertical: 8,
                  borderWidth: 1,
                  flexDirection: "row",
                  alignItems: "flex-end",
                  backgroundColor: "#F8FAFC",
                  borderColor: "#E2E8F0",
                }}
              >
                <TextInput
                  style={{ flex: 1, fontSize: 15, maxHeight: 128, paddingVertical: 4, color: "#1E293B" }}
                  placeholder="Type a message..."
                  value={text}
                  onChangeText={setText}
                  multiline
                  placeholderTextColor="#94A3B8"
                />
                <Pressable style={{ paddingBottom: 4, paddingLeft: 8 }}>
                  <Feather name="smile" size={20} color="#94A3B8" />
                </Pressable>
              </View>

              <Pressable
                onPress={handleSend}
                disabled={!text.trim() || sending}
                style={{
                  width: 48,
                  height: 48,
                  borderRadius: 24,
                  alignItems: "center",
                  justifyContent: "center",
                  backgroundColor: text.trim() ? "#9333EA" : "#E2E8F0",
                  elevation: text.trim() ? 4 : 0,
                }}
              >
                {sending ? (
                  <ActivityIndicator size="small" color="#fff" />
                ) : (
                  <Ionicons name="send" size={20} color="white" style={{ marginLeft: 3 }} />
                )}
              </Pressable>
            </View>
          </SafeAreaView>
        )}
      </KeyboardAvoidingView>

      {/* ── MODALS ─────────────────────────────────────────────────────────── */}
      <TerminateConfirmModal
        visible={showTerminateConfirm}
        onCancel={() => setShowTerminateConfirm(false)}
        onConfirm={handleTerminateConfirmed}
        terminating={terminating}
      />
      <ReviewModal
        visible={showReviewModal}
        mentorName={getTitle()}
        onClose={() => {
          setShowReviewModal(false);
          setTimeout(() => router.back(), 300);
        }}
        onSubmit={handleSubmitReview}
        submitting={submittingReview}
      />
    </View>
  );
};

export default ChatScreen;