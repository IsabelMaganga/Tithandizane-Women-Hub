import React, { useState, useEffect, useCallback } from "react";
import {
  View,
  Text,
  ScrollView,
  Pressable,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { Ionicons, MaterialIcons } from "@expo/vector-icons";
import {
  getMentorAvailability,
  updateMentorAvailability,
  type MentorAvailability,
  type AvailabilityDayKey as DayKey,
  type MentorTimeSlot as TimeSlot,
} from "../../services/api";

import { AuthProvider, useAuth } from "@/context/AuthContext";

type Availability = MentorAvailability;

// ─── Constants ────────────────────────────────────────────────────────────────

const DAYS: { key: DayKey; label: string }[] = [
  { key: "Mon", label: "Monday" },
  { key: "Tue", label: "Tuesday" },
  { key: "Wed", label: "Wednesday" },
  { key: "Thu", label: "Thursday" },
  { key: "Fri", label: "Friday" },
  { key: "Sat", label: "Saturday" },
  { key: "Sun", label: "Sunday" },
];

// Half-hour increments from 06:00 to 22:00
const TIME_OPTIONS: string[] = (() => {
  const times: string[] = [];
  for (let h = 6; h <= 22; h++) {
    ["00", "30"].forEach((m) => {
      times.push(`${String(h).padStart(2, "0")}:${m}`);
    });
  }
  return times;
})();

const DEFAULT_START = "09:00";
const DEFAULT_END = "17:00";

// ─── Sub-components ───────────────────────────────────────────────────────────

/** Tappable pill for a day of the week */
const DayPill = ({
  day,
  active,
  onPress,
}: {
  day: string;
  active: boolean;
  onPress: () => void;
}) => (
  <Pressable
    onPress={onPress}
    className={`px-4 py-2 rounded-full border mr-2 mb-2 ${
      active
        ? "bg-violet-600 border-violet-600"
        : "bg-white border-gray-200"
    }`}
  >
    <Text
      className={`text-sm font-semibold ${
        active ? "text-white" : "text-gray-500"
      }`}
    >
      {day}
    </Text>
  </Pressable>
);

/** Horizontal time picker — scrollable list of options */
const TimePicker = ({
  label,
  value,
  onChange,
  minValue,
}: {
  label: string;
  value: string;
  onChange: (v: string) => void;
  minValue?: string;
}) => {
  const options = minValue
    ? TIME_OPTIONS.filter((t) => t > minValue)
    : TIME_OPTIONS;

  return (
    <View className="flex-1">
      <Text className="text-xs text-gray-400 mb-1 font-medium">{label}</Text>
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        className="flex-row"
        contentContainerStyle={{ paddingRight: 8 }}
      >
        {options.map((t) => {
          const selected = t === value;
          return (
            <TouchableOpacity
              key={t}
              onPress={() => onChange(t)}
              className={`px-3 py-2 rounded-lg mr-2 border ${
                selected
                  ? "bg-violet-600 border-violet-600"
                  : "bg-gray-50 border-gray-200"
              }`}
            >
              <Text
                className={`text-xs font-semibold ${
                  selected ? "text-white" : "text-gray-600"
                }`}
              >
                {formatTime(t)}
              </Text>
            </TouchableOpacity>
          );
        })}
      </ScrollView>
    </View>
  );
};

/** Slot card for a single active day */
const SlotCard = ({
  dayKey,
  label,
  slot,
  onUpdate,
  onRemove,
}: {
  dayKey: DayKey;
  label: string;
  slot: TimeSlot;
  onUpdate: (day: DayKey, field: "start" | "end", value: string) => void;
  onRemove: (day: DayKey) => void;
}) => (
  <View className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-3">
    {/* Card header */}
    <View className="flex-row items-center justify-between mb-3">
      <View className="flex-row items-center space-x-2">
        <View className="w-8 h-8 rounded-xl bg-violet-100 items-center justify-center">
          <Ionicons name="calendar" size={16} color="#7C3AED" />
        </View>
        <Text className="text-base font-semibold text-gray-800">{label}</Text>
      </View>
      <TouchableOpacity
        onPress={() => onRemove(dayKey)}
        className="p-1"
        hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
      >
        <Ionicons name="close-circle" size={20} color="#D1D5DB" />
      </TouchableOpacity>
    </View>

    {/* Duration summary */}
    <View className="flex-row items-center bg-violet-50 rounded-xl px-3 py-2 mb-3">
      <Ionicons name="time-outline" size={14} color="#7C3AED" />
      <Text className="text-xs text-violet-700 font-medium ml-1">
        {formatTime(slot.start)} – {formatTime(slot.end)}{" "}
        <Text className="text-violet-500 font-normal">
          ({durationLabel(slot.start, slot.end)})
        </Text>
      </Text>
    </View>

    {/* Time pickers */}
    <View className="flex-row space-x-3">
      <TimePicker
        label="From"
        value={slot.start}
        onChange={(v) => onUpdate(dayKey, "start", v)}
      />
      <View className="items-center justify-end pb-2">
        <MaterialIcons name="arrow-forward" size={16} color="#9CA3AF" />
      </View>
      <TimePicker
        label="Until"
        value={slot.end}
        minValue={slot.start}
        onChange={(v) => onUpdate(dayKey, "end", v)}
      />
    </View>
  </View>
);

// ─── Helpers ──────────────────────────────────────────────────────────────────

function formatTime(t: string): string {
  const [hStr, mStr] = t.split(":");
  const h = parseInt(hStr, 10);
  const ampm = h < 12 ? "AM" : "PM";
  const h12 = h % 12 || 12;
  return `${h12}:${mStr} ${ampm}`;
}

function durationLabel(start: string, end: string): string {
  const [sh, sm] = start.split(":").map(Number);
  const [eh, em] = end.split(":").map(Number);
  const totalMins = eh * 60 + em - (sh * 60 + sm);
  if (totalMins <= 0) return "";
  const h = Math.floor(totalMins / 60);
  const m = totalMins % 60;
  return h > 0 ? `${h}h${m > 0 ? ` ${m}m` : ""}` : `${m}m`;
}

// ─── Main Screen ──────────────────────────────────────────────────────────────

const MentorAvailabilityScreen = () => {
  const router = useRouter();
  const [availability, setAvailability] = useState<Availability>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  // ── Fetch current availability ──
  useEffect(() => {
    (async () => {
      try {
        const data = await getMentorAvailability();
        setAvailability(data ?? {});
      } catch (err) {
        Alert.alert("Error", "Could not load your availability.");
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  // ── Toggle a day on/off ──
  const toggleDay = useCallback((key: DayKey) => {
    setAvailability((prev) => {
      if (prev[key]) {
        const next = { ...prev };
        delete next[key];
        return next;
      }
      return { ...prev, [key]: { start: DEFAULT_START, end: DEFAULT_END } };
    });
  }, []);

 
  const updateSlot = useCallback(
    (day: DayKey, field: "start" | "end", value: string) => {
      setAvailability((prev) => {
        const slot = prev[day];
        if (!slot) return prev;

        let updated = { ...slot, [field]: value };

        // Auto-push end time if it would be <= start
        if (field === "start" && updated.end <= value) {
          const idx = TIME_OPTIONS.indexOf(value);
          updated.end = TIME_OPTIONS[idx + 1] ?? value;
        }

        return { ...prev, [day]: updated };
      });
    },
    []
  );

  // ── Remove a day slot ──
  const removeDay = useCallback((key: DayKey) => {
    setAvailability((prev) => {
      const next = { ...prev };
      delete next[key];
      return next;
    });
  }, []);

  // ── Save ──
  const handleSave = async () => {
    if (Object.keys(availability).length === 0) {
      Alert.alert(
        "No days selected",
        "Please select at least one day before saving."
      );
      return;
    }

    setSaving(true);
    try {
      await updateMentorAvailability(availability);
      Alert.alert("Saved", "Your availability has been updated.", [
        { text: "OK", onPress: () => router.back() },
      ]);
    } catch (err: any) {
      const message =
        err?.message || "Could not save your availability. Please try again.";
      Alert.alert("Error", message);
    } finally {
      setSaving(false);
    }
  };

  const activeDays = DAYS.filter((d) => !!availability[d.key]);



  if (loading) {
    return (
      <SafeAreaView className="flex-1 bg-gray-50 items-center justify-center">
        <ActivityIndicator size="large" color="#7C3AED" />
        <Text className="text-gray-400 text-sm mt-3">
          Loading availability…
        </Text>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-gray-50">
      {/* ── Header ── */}
      <View className="flex-row items-center px-4 py-3 bg-white border-b border-gray-100">
        <TouchableOpacity
          onPress={() => router.back()}
          className="mr-3 p-1"
          hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
        >
          <Ionicons name="arrow-back" size={22} color="#374151" />
        </TouchableOpacity>
        <View className="flex-1">
          <Text className="text-lg font-bold text-gray-900">My availability</Text>
          <Text className="text-xs text-gray-400">
            Set the days and hours you're open for sessions
          </Text>
        </View>
      </View>

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 120 }}
        className="px-4"
      >
        {/* ── Day selector ── */}
        <Text className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-6 mb-3 ml-1">
          Available days
        </Text>
        <View className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
          <Text className="text-sm text-gray-500 mb-3">
            Tap a day to toggle it on or off.
          </Text>
          <View className="flex-row flex-wrap">
            {DAYS.map((d) => (
              <DayPill
                key={d.key}
                day={d.key}
                active={!!availability[d.key]}
                onPress={() => toggleDay(d.key)}
              />
            ))}
          </View>
        </View>

        {/* ── Time slots ── */}
        <Text className="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">
          Time slots{" "}
          {activeDays.length > 0 && (
            <Text className="text-violet-500">({activeDays.length} day{activeDays.length > 1 ? "s" : ""})</Text>
          )}
        </Text>

        {activeDays.length === 0 ? (
          <View className="bg-white rounded-2xl border border-gray-100 p-8 items-center">
            <Ionicons name="calendar-outline" size={40} color="#D1D5DB" />
            <Text className="text-gray-400 text-sm mt-3 text-center">
              No days selected yet.{"\n"}Tap a day above to add a time slot.
            </Text>
          </View>
        ) : (
          activeDays.map((d) => (
            <SlotCard
              key={d.key}
              dayKey={d.key}
              label={d.label}
              slot={availability[d.key]!}
              onUpdate={updateSlot}
              onRemove={removeDay}
            />
          ))
        )}
      </ScrollView>

      {/* ── Save button (floating) ── */}
      <View className="absolute bottom-0 left-0 right-0 px-4 pb-6 pt-3 bg-gray-50 border-t border-gray-100">
        <TouchableOpacity
          onPress={handleSave}
          disabled={saving}
          activeOpacity={0.85}
          className={`py-4 rounded-2xl items-center justify-center flex-row space-x-2 ${
            saving ? "bg-violet-400" : "bg-violet-600"
          }`}
        >
          {saving ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Ionicons name="checkmark-circle" size={20} color="#fff" />
          )}
          <Text className="text-white font-bold text-base ml-2">
            {saving ? "Saving…" : "Save availability"}
          </Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
};

export default MentorAvailabilityScreen;