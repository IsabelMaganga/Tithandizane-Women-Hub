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
} from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { Feather, Ionicons } from "@expo/vector-icons";
import { LegendList } from "@legendapp/list";
import {
  getMessages,
  sendMessage,
  getConversation,
  createConversation,
  BASE_URL,
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
  name: string;
  is_group: boolean;
}

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
        <View
          style={{
            flex: 1,
            backgroundColor: "rgba(15, 10, 30, 0.6)",
            justifyContent: "flex-end",
          }}
        >
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
              <View
                style={{
                  width: 40,
                  height: 4,
                  backgroundColor: "#E2E8F0",
                  borderRadius: 2,
                  alignSelf: "center",
                  marginBottom: 24,
                }}
              />
              <View style={{ alignItems: "center", marginBottom: 24 }}>
                <View
                  style={{
                    width: 64,
                    height: 64,
                    borderRadius: 32,
                    backgroundColor: "#EDE9FE",
                    alignItems: "center",
                    justifyContent: "center",
                    marginBottom: 14,
                    borderWidth: 2,
                    borderColor: "#DDD6FE",
                  }}
                >
                  <Text style={{ fontSize: 26, fontWeight: "700", color: "#7C3AED" }}>
                    {mentorName?.charAt(0)?.toUpperCase() ?? "M"}
                  </Text>
                </View>
                <Text style={{ fontSize: 20, fontWeight: "700", color: "#0F172A", marginBottom: 4 }}>
                  How was your session?
                </Text>
                <Text style={{ fontSize: 14, color: "#64748B", textAlign: "center" }}>
                  Share your experience with{" "}
                  <Text style={{ fontWeight: "600", color: "#4C1D95" }}>{mentorName}</Text>
                </Text>
              </View>
              <View style={{ height: 1, backgroundColor: "#F1F5F9", marginBottom: 24 }} />
              <View style={{ marginBottom: 24 }}>
                <Text
                  style={{
                    fontSize: 13,
                    fontWeight: "600",
                    color: "#475569",
                    textTransform: "uppercase",
                    letterSpacing: 0.8,
                    textAlign: "center",
                    marginBottom: 14,
                  }}
                >
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
                <Text
                  style={{
                    fontSize: 13,
                    fontWeight: "600",
                    color: "#475569",
                    textTransform: "uppercase",
                    letterSpacing: 0.8,
                    marginBottom: 10,
                  }}
                >
                  Leave a note{" "}
                  <Text style={{ color: "#94A3B8", fontWeight: "400", textTransform: "none", letterSpacing: 0 }}>
                    (optional)
                  </Text>
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
                  style={{
                    backgroundColor: "#F8FAFC",
                    borderWidth: 1.5,
                    borderColor: comment.length > 0 ? "#A78BFA" : "#E2E8F0",
                    borderRadius: 16,
                    padding: 14,
                    fontSize: 15,
                    color: "#1E293B",
                    minHeight: 110,
                    lineHeight: 22,
                  }}
                />
                <Text style={{ textAlign: "right", marginTop: 6, fontSize: 11, color: "#94A3B8" }}>
                  {comment.length}/500
                </Text>
              </View>
              <View style={{ gap: 10 }}>
                <Pressable
                  onPress={handleSubmit}
                  disabled={submitting || rating === 0}
                  style={({ pressed }) => ({
                    backgroundColor: rating === 0 ? "#E2E8F0" : pressed ? "#6D28D9" : "#7C3AED",
                    borderRadius: 16,
                    paddingVertical: 16,
                    alignItems: "center",
                    justifyContent: "center",
                  })}
                >
                  {submitting ? (
                    <ActivityIndicator size="small" color="#fff" />
                  ) : (
                    <Text style={{ fontSize: 16, fontWeight: "700", color: rating === 0 ? "#94A3B8" : "#FFFFFF", letterSpacing: 0.2 }}>
                      Submit Review
                    </Text>
                  )}
                </Pressable>
                <Pressable
                  onPress={handleClose}
                  style={({ pressed }) => ({
                    borderRadius: 16,
                    paddingVertical: 14,
                    alignItems: "center",
                    opacity: pressed ? 0.5 : 1,
                  })}
                >
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

const TerminateConfirmModal = ({
  visible,
  onCancel,
  onConfirm,
  terminating,
}: {
  visible: boolean;
  onCancel: () => void;
  onConfirm: () => void;
  terminating: boolean;
}) => (
  <Modal visible={visible} transparent animationType="fade" statusBarTranslucent>
    <TouchableWithoutFeedback onPress={onCancel}>
      <View style={{ flex: 1, backgroundColor: "rgba(15, 10, 30, 0.6)", justifyContent: "center", paddingHorizontal: 24 }}>
        <TouchableWithoutFeedback>
          <View style={{ backgroundColor: "#FFFFFF", borderRadius: 24, padding: 28 }}>
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
            <Text style={{ fontSize: 18, fontWeight: "700", color: "#0F172A", textAlign: "center", marginBottom: 8 }}>
              End this session?
            </Text>
            <Text style={{ fontSize: 14, color: "#64748B", textAlign: "center", lineHeight: 20, marginBottom: 24 }}>
              This will close the session permanently. The mentee will be asked to leave a review.
            </Text>
            <View style={{ flexDirection: "row", gap: 10 }}>
              <Pressable
                onPress={onCancel}
                style={({ pressed }) => ({
                  flex: 1,
                  borderRadius: 14,
                  paddingVertical: 14,
                  alignItems: "center",
                  backgroundColor: pressed ? "#F1F5F9" : "#F8FAFC",
                  borderWidth: 1.5,
                  borderColor: "#E2E8F0",
                })}
              >
                <Text style={{ fontSize: 15, fontWeight: "600", color: "#475569" }}>Keep Going</Text>
              </Pressable>
              <Pressable
                onPress={onConfirm}
                disabled={terminating}
                style={({ pressed }) => ({
                  flex: 1,
                  borderRadius: 14,
                  paddingVertical: 14,
                  alignItems: "center",
                  backgroundColor: pressed ? "#DC2626" : "#EF4444",
                })}
              >
                {terminating ? (
                  <ActivityIndicator size="small" color="#fff" />
                ) : (
                  <Text style={{ fontSize: 15, fontWeight: "700", color: "#FFFFFF" }}>End Session</Text>
                )}
              </Pressable>
            </View>
          </View>
        </TouchableWithoutFeedback>
      </View>
    </TouchableWithoutFeedback>
  </Modal>
);

const ChatScreen = () => {
  const params = useLocalSearchParams<{
    id: string;
    isNew?: string;
    name?: string;
    sessionId?: string;
    isMentor?: string;
  }>();

  const id            = Array.isArray(params.id)        ? params.id[0]        : params.id;
  const isNew         = Array.isArray(params.isNew)      ? params.isNew[0]     : params.isNew;
  const name          = Array.isArray(params.name)       ? params.name[0]      : params.name;
  const sessionId     = Array.isArray(params.sessionId)  ? params.sessionId[0] : params.sessionId;
  const isMentorParam = Array.isArray(params.isMentor)   ? params.isMentor[0]  : params.isMentor;

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
  const [sessionEnded, setSessionEnded]                 = useState(false);

  // ─── Parse sessionId safely ───────────────────────────────────────────────
  const activeSessionId: number | null = (() => {
    if (!sessionId || sessionId === "undefined" || sessionId === "null") return null;
    const n = Number(sessionId);
    return isNaN(n) || n === 0 ? null : n;
  })();

  // ─── FIX: Dual mentor check ───────────────────────────────────────────────
  // Primary:  user.role from AuthContext (most reliable)
  // Fallback: isMentor URL param (passed from session screens)
  // Both are checked so the button shows even if one source is missing/stale
  const isMentor =
    user?.role === "mentor" ||
    isMentorParam === "true";

  // ─── FIX: Show End button conditions ─────────────────────────────────────
  // Requires ALL of:
  //   1. isMentor   — user is a mentor (role or param)
  //   2. activeSessionId !== null — a real session ID was passed
  //   3. !sessionEnded — session is still open
const showEndButton = isMentor && activeSessionId !== null && !sessionEnded;

  

  const unsubscribeRef   = useRef<(() => void) | null>(null);
  const optimisticIdsRef = useRef<Set<number>>(new Set());
  const confirmedIdsRef  = useRef<Set<number>>(new Set());

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
    } catch {
      // silently fail
    }
  };

  const handleTerminateConfirmed = async () => {
    if (!activeSessionId || !token) return;
    setTerminating(true);
    try {
      const res = await fetch(
        `${BASE_URL}/api/mentorship/sessions/${activeSessionId}/terminate`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw new Error(body?.message ?? "Failed to terminate session");
      }

      setSessionEnded(true);
      setShowTerminateConfirm(false);
      // Mentors don't need to review — skip the review modal for them
      // The mentee will be prompted on their end via WebSocket event
    } catch (err: any) {
      console.error("Terminate session error:", err);
      Alert.alert("Error", err?.message ?? "Could not end the session. Please try again.");
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
      const res = await fetch(
        `${BASE_URL}/api/mentorship/sessions/${activeSessionId}/review`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify({ rating, comment }),
        }
      );

      if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw new Error(body?.message ?? "Failed to submit review");
      }

      setShowReviewModal(false);
      setTimeout(() => router.back(), 300);
    } catch (err: any) {
      console.error("Submit review error:", err);
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
          m.id === optimisticId
            ? { ...confirmed, status: "sent" as MessageStatus }
            : m
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
    return conversation.is_group ? conversation.name : conversation.name || name || "";
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

      {/* HEADER */}
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

        {/* ─── END SESSION button — visible only for mentors with active session ─── */}
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

        {/* CLOSED badge */}
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

      {/* SESSION ENDED BANNER */}
      {sessionEnded && (
        <View
          style={{
            backgroundColor: "#F8FAFC",
            borderBottomWidth: 1,
            borderBottomColor: "#E2E8F0",
            paddingVertical: 10,
            paddingHorizontal: 16,
            flexDirection: "row",
            alignItems: "center",
            justifyContent: "center",
            gap: 6,
          }}
        >
          <Ionicons name="information-circle" size={15} color="#94A3B8" />
          <Text style={{ fontSize: 12, color: "#94A3B8", fontWeight: "500" }}>
            This session has ended. No new messages can be sent.
          </Text>
        </View>
      )}

      {/* MESSAGES */}
      <KeyboardAvoidingView
        className="flex-1"
        behavior={Platform.OS === "ios" ? "padding" : undefined}
      >
        <LegendList
          data={messages}
          estimatedItemSize={70}
          contentContainerStyle={{ paddingHorizontal: 16, paddingTop: 16, paddingBottom: 20 }}
          keyExtractor={(item) => String(item.id)}
          renderItem={({ item }) => {
            const isMe   = item.sender_id === user?.id;
            const status = item.status ?? (isMe ? "sent" : undefined);

            return (
              <View className={`mb-3 max-w-[85%] ${isMe ? "self-end" : "self-start"}`}>
                <View
                  className={`px-4 py-3 rounded-3xl ${
                    isMe
                      ? "bg-purple-600 rounded-tr-none shadow-md shadow-purple-200"
                      : "bg-white border border-slate-100 rounded-tl-none shadow-sm"
                  }`}
                >
                  <Text className={`${isMe ? "text-white" : "text-slate-800"} text-[15px] leading-5`}>
                    {item.message}
                  </Text>
                </View>
                <View className={`flex-row items-center mt-0.5 px-1 ${isMe ? "justify-end" : "justify-start"}`}>
                  {!isMe && (
                    <Text className="text-[10px] text-slate-400">{item.sender?.name ?? ""}</Text>
                  )}
                  {isMe && status && (
                    <View className="flex-row items-center space-x-0.5">
                      <Text className="text-[10px] text-slate-400 mr-0.5">
                        {status === "sending"
                          ? "Sending…"
                          : status === "sent"
                          ? "Sent"
                          : status === "delivered"
                          ? "Delivered"
                          : "Read"}
                      </Text>
                      <ReadReceipt status={status} />
                    </View>
                  )}
                </View>
              </View>
            );
          }}
          ListEmptyComponent={
            <View className="items-center mt-10 px-10">
              <Text className="text-slate-300 text-center font-medium">
                Your conversation starts here. Messages are encrypted and private.
              </Text>
            </View>
          }
        />

        {/* INPUT BAR */}
        <SafeAreaView edges={["bottom"]} className="bg-white border-t border-slate-100">
          <View className="p-3 flex-row items-end space-x-2">
            <View
              className={`flex-1 rounded-[24px] px-4 py-2 border flex-row items-end ${
                sessionEnded ? "bg-slate-100 border-slate-200" : "bg-slate-50 border-slate-200"
              }`}
            >
              <TextInput
                className="flex-1 text-[15px] max-h-32 py-1"
                style={{ color: sessionEnded ? "#CBD5E1" : "#1E293B" }}
                placeholder={sessionEnded ? "Session has ended" : "Type a message..."}
                value={text}
                onChangeText={setText}
                multiline
                placeholderTextColor="#94A3B8"
                editable={!sessionEnded}
              />
              {!sessionEnded && (
                <Pressable className="pb-1 pl-2">
                  <Feather name="smile" size={20} color="#94A3B8" />
                </Pressable>
              )}
            </View>

            <Pressable
              onPress={handleSend}
              disabled={!text.trim() || sending || sessionEnded}
              className={`w-12 h-12 rounded-full items-center justify-center shadow-lg ${
                text.trim() && !sessionEnded ? "bg-purple-600 shadow-purple-300" : "bg-slate-200 shadow-none"
              }`}
            >
              {sending ? (
                <ActivityIndicator size="small" color="#fff" />
              ) : (
                <Ionicons name="send" size={20} color="white" style={{ marginLeft: 3 }} />
              )}
            </Pressable>
          </View>
        </SafeAreaView>
      </KeyboardAvoidingView>

      {/* MODALS */}
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