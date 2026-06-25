import React from 'react';
import { View, Text, Image, Pressable } from 'react-native';
import { FontAwesome5, Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

export type Mentor = {
  id: number | string;
  name: string;
  email?: string;
  phone?: string | null;
  location?: string | null;
  photo?: string | null;
  avatar?: string;
  expertise: string[];
  bio?: string;
  availability?: string | null;
  available_days?: string[];
  available_time_start?: string | null;
  available_time_end?: string | null;
  linkedin_url?: string | null;
  twitter_url?: string | null;
  website_url?: string | null;
  average_rating?: number | null;
  rating?: number | null;
  total_sessions?: number;
  status?: string;
};

// ─── Heart Rating Sub-component ──────────────────────────────────────────────

const HeartRating = ({ rating }: { rating: number | null | undefined }) => {
  if (!rating) {
    return (
      <View className="bg-purple-50 px-2 py-0.5 rounded-md">
        <Text className="text-purple-600 text-[11px] font-bold">New Mentor</Text>
      </View>
    );
  }

  const roundedRating = Math.round(rating);

  return (
    <View className="flex-row items-center space-x-0.5">
      {[1, 2, 3, 4, 5].map((starIndex) => (
        <FontAwesome5
          key={starIndex}
          name="heart"
          size={12}
          color="#8A4FFF"
          solid={starIndex <= roundedRating}
        />
      ))}
      <Text className="text-slate-500 text-xs font-bold ml-1.5">
        ({Number(rating).toFixed(1)})
      </Text>
    </View>
  );
};

// ─── MentorCard ──────────────────────────────────────────────────────────────

type MentorCardProps = {
  mentor: Mentor;
};

const MentorCard = ({ mentor }: MentorCardProps) => {
  const router = useRouter();

  const expertiseArea =
    Array.isArray(mentor.expertise) && mentor.expertise.length > 0
      ? mentor.expertise.join(', ')
      : 'Mentor';

  const avatarUrl =
    mentor.avatar ||
    mentor.photo ||
    `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name || 'M')}&background=8b5cf6&color=fff`;

  const displayRating = mentor.average_rating ?? mentor.rating ?? null;

  return (
    <View className="bg-white rounded-3xl mb-5 overflow-hidden shadow-xs border border-slate-100">
      <View className="p-5">

        {/* ── Identity Frame ── */}
        <View className="flex-row items-start justify-between">
          <View className="flex-row flex-1 items-center">
            <View className="relative">
              <Image
                source={{ uri: avatarUrl }}
                className="w-16 h-16 rounded-2xl"
                style={{ backgroundColor: '#f0f0f0' }}
              />
              <View className="absolute -bottom-1 -right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white" />
            </View>

            <View className="ml-4 flex-1">
              <Text className="text-slate-900 text-lg font-bold" numberOfLines={1}>
                {mentor.name}
              </Text>

              <View className="mt-1">
                <HeartRating rating={displayRating} />
              </View>

              <View className="bg-violet-50 self-start px-2 py-0.5 rounded-md mt-2">
                <Text
                  className="text-violet-600 text-[10px] font-bold uppercase tracking-wider"
                  numberOfLines={1}
                >
                  {expertiseArea}
                </Text>
              </View>
            </View>
          </View>

          <Pressable className="bg-slate-50 p-2 rounded-full active:bg-slate-100">
            <Feather name="bookmark" size={18} color="#64748b" />
          </Pressable>
        </View>

        {/* ── Bio ── */}
        {mentor.bio ? (
          <Text className="text-slate-600 text-sm mt-4 leading-5" numberOfLines={3}>
            {mentor.bio}
          </Text>
        ) : null}

        {/* ── Availability ── */}
        <View className="mt-4 pt-4 border-t border-slate-100">
          <View className="flex-row items-center mb-3">
            <MaterialCommunityIcons name="calendar-clock" size={18} color="#8b5cf6" />
            <Text className="text-slate-500 text-xs ml-2 font-medium">
              Available: {mentor.available_time_start || '09:00'} -{' '}
              {mentor.available_time_end || '17:00'}
            </Text>
          </View>

          {Array.isArray(mentor.available_days) && mentor.available_days.length > 0 && (
            <View className="flex-row flex-wrap gap-2">
              {mentor.available_days.map((day, index) => (
                <View key={index} className="bg-slate-100 px-3 py-1 rounded-lg">
                  <Text className="text-slate-600 text-[10px] font-semibold capitalize">
                    {day}
                  </Text>
                </View>
              ))}
            </View>
          )}
        </View>

        {/* ── CTA ── */}
        <Pressable
          className="bg-purple-600 h-14 rounded-2xl flex-row justify-center items-center border border-purple-600 active:opacity-85 mt-4"
          onPress={() =>
            router.push({
              pathname: '/mentorship-request',
              params: {
                mentorId: mentor.id.toString(),
                mentorName: mentor.name,
              },
            })
          }
        >
          <Text className="text-white text-center font-bold text-base">
            Book Free Session
          </Text>
        </Pressable>

      </View>
    </View>
  );
};

export default MentorCard;
