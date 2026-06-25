import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';

// Types
export interface User {
  id: number;
  name: string;
  email: string;
  role: 'user' | 'mentor';
  phone?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface RegisterData {
  name: string;
  email: string;
  phone?: string | null;
  password: string;
  password_confirmation: string;
}

export interface LoginResponse {
  user: User;
  token: string;
}

export interface HygieneArticle {
  id: number;
  title: string;
  content: string;
  category: string;
  image_url?: string;
  created_at: string;
}

export interface GuidanceContent {
  id: number;
  mentor_id: number;
  title: string;
  body: string;
  photo_url?: string | null;
  category: 'menstrual_hygiene' | 'general';
  status: 'published' | 'unpublished';
  language: 'english';
  mentor_name?: string;
  created_at: string;
  updated_at: string;
}

export interface GuidanceContentPayload {
  title: string;
  body: string;
  category: 'menstrual_hygiene' | 'general';
  status?: 'published' | 'unpublished';
  photo?: { uri: string; type?: string; name?: string } | null;
  remove_photo?: boolean;
}

export interface EmergencyContact {
  id: number;
  name: string;
  phone: string;
  organization: string;
  region?: string;
}

export interface Mentor {
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
  average_rating?: number | null; 
  rating?: number | null;         
  total_sessions?: number;
  status?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Conversation {
  id: number;
  mentor_id?: number;
  user_id?: number;
  name?: string | null;
  is_group?: boolean;
  mentor?: Mentor;
  user?: User;
  last_message?: string;
  created_at?: string;
}

export interface Message {
  id: number;
  conversation_id: number;
  sender_id: number;
  message: string;
  created_at: string;
}

export interface ChatListItem {
  id: number;
  name?: string;
  is_group: boolean;
  avatar?: string;
  unread_count: number;
  participants?: {
    id: number;
    name: string;
    avatar?: string;
    is_online?: boolean;
  }[];
  messages?: {
    id: number;
    sender_id: number;
    message: string;
    type?: string;
    image?: string;
    file?: string;
    audio?: string;
    video?: string;
    attachment_type?: string;
    created_at: string;
  }[];
  created_at: string;
}

export interface HarassmentReport {
  id: number;
  reference_number?: string;
  incident_type: string;
  incident_title: string;
  incident_description: string;
  incident_location?: string;
  incident_date?: string;
  perpetrator_info?: string;
  is_anonymous: boolean;
  status: 'pending' | 'reviewing' | 'assigned' | 'resolved' | 'dismissed';
  admin_response?: string;
  responded_at?: string;
  assigned_mentor?: { id: number; name: string } | null;
  submitted_at?: string;
  has_response?: boolean;
  response?: string;
  created_at: string;
}

export interface ReportTracking {
  reference_number: string;
  incident_type: string;
  status: 'pending' | 'reviewing' | 'assigned' | 'resolved' | 'dismissed';
  submitted_at: string;
  has_response: boolean;
  response?: string | null;
  responded_at?: string | null;
  assigned_mentor?: { name: string } | null;
}

export interface AppNotification {
  id: number;
  type: string;
  title: string;
  message: string;
  is_read: boolean;
  report_id?: number | null;
  data?: Record<string, unknown>;
  created_at: string;
}

export interface MentorshipSession {
  id: number;
  mentor_id: number;
  mentee_id: number;
  topic: string;
  message?: string | null;
  status: 'pending' | 'accepted' | 'declined' | 'completed' | 'missed';
  mentor_notes?: string | null;
  scheduled_at?: string | null;
  requested_date?: string | null;
  requested_time_from?: string | null;
  requested_time_to?: string | null;
  is_missed: boolean;
  missed_at?: string | null;
  conversation_started_at?: string | null;
  mentor?: Mentor;
  mentee?: User;
  review?: MentorReview | null;
  created_at: string;
  updated_at: string;
}

export interface MentorReview {
  id: number;
  mentorship_session_id: number;
  reviewer_id: number;
  mentor_id: number;
  rating: number;
  comment?: string | null;
  reviewer?: User;
  created_at: string;
}

export interface MentorshipRequestData {
  mentor_id: number;
  topic: string;
  message?: string;
  requested_date: string;        // e.g. "2026-06-25"
  requested_time_from: string;   // e.g. "09:00"
  requested_time_to: string;     // e.g. "10:00"
}

export interface CommunityComment {
  id: number;
  user_id: number;
  comment: string;
  user?: User;
  created_at: string;
}

export interface CommunityPost {
  id: number;
  user_id: number;
  category: string;
  text: string;
  user: User;
  comments: CommunityComment[];
  likes_count: number;
  comments_count: number;
  created_at: string;
}

// computer or wifi ip
const COMPUTER_IP = '192.168.43.103';
const BACKEND_PORT = '8000';

// Function to get the correct base URL based on platform
const getBaseURL = (): string => {
  if (__DEV__) {
    console.log('📱 Platform:', Platform.OS);

    if (Platform.OS === 'android') {
      return `http://192.168.43.103:8000/api/v1`;
    }

    if (Platform.OS === 'ios') {
      return `http://192.168.38.205:8000/api/v1`;
    }

    return `http://192.168.1.132:8000/api/v1`;
  } else {
    return 'http://192.168.1.132:8000/api/v1';
  }
};

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 30000,
});

// Log the base URL on startup
console.log('🌐 API Base URL:', api.defaults.baseURL);
console.log('💡 Make sure:');
console.log(`   1. Laravel is running on port ${BACKEND_PORT}`);
console.log(`   2. Phone is connected to WiFi (same network as computer)`);
console.log(`   3. Can access ${api.defaults.baseURL}/mentors/active from phone browser`);

// Request interceptor - DON'T add token for registration
api.interceptors.request.use(
  async (config: InternalAxiosRequestConfig): Promise<InternalAxiosRequestConfig> => {
    // Public routes that don't need authentication
    const publicRoutes = ['/register', '/login', '/password/forgot', '/password/reset', '/mentors/active', '/mentors/', '/harassment-reports', '/anonymous-reports'];
    const isPublicRoute = publicRoutes.some(route => config.url?.includes(route));

    if (!isPublicRoute) {
      const token = await AsyncStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    }

    console.log(`📤 ${config.method?.toUpperCase()} ${config.baseURL}${config.url}`);
    if (config.data) {
      console.log('📦 Request data:', config.data);
    }
    return config;
  },
  (error: AxiosError) => {
    console.error('Request interceptor error:', error.message);
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    console.log(`📥 Response: ${response.status} ${response.config.url}`);
    return response;
  },
  async (error: AxiosError) => {
    // Handle Network Errors
    if (error.code === 'ERR_NETWORK' || error.message === 'Network Error') {
      console.error('🔌 NETWORK ERROR:');
      console.error('   Cannot connect to:', error.config?.baseURL);
      console.error('   ');
      console.error('   ⚠️ TROUBLESHOOTING STEPS:');
      console.error('   1. Is Laravel running? Run: php artisan serve --host=0.0.0.0 --port=8000');
      console.error(`   2. Is Laravel on port ${BACKEND_PORT}?`);
      console.error('   3. Is phone on the SAME WiFi as computer?');
      console.error(`   4. Can phone access: ${error.config?.baseURL}/mentors/active`);
      console.error('   5. Windows Firewall might be blocking port 8000');
      console.error('   ');

      throw new Error('Unable to connect to server. Please check:\n' +
        '• Backend is running (php artisan serve --host=0.0.0.0)\n' +
        '• Phone and computer on same WiFi\n' +
        `• Can access ${COMPUTER_IP}:8000 from phone browser\n` +
        '• Firewall allows port 8000');
    }

    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      console.log('🔐 Unauthorized - clearing token');
      await AsyncStorage.removeItem('token');
    }

    // Log other errors
    if (error.response) {
      console.error('❌ API Error:', {
        status: error.response.status,
        data: error.response.data,
        url: error.config?.url,
      });
    }

    return Promise.reject(error);
  }
);

// User Registration
export const registerUser = async (userData: RegisterData): Promise<LoginResponse> => {
  try {
    console.log('📝 Registering user:', userData.email);
    console.log('📍 Endpoint:', `${api.defaults.baseURL}/register`);

    const response = await api.post<LoginResponse>('/register', userData);

    console.log('✅ Registration successful!');

    if (response.data.token) {
      await AsyncStorage.setItem('token', response.data.token);
    }

    return response.data;
  } catch (error: any) {
    console.error('❌ Registration error:', error.message);

    if (error.message?.includes('Unable to connect')) {
      throw error;
    }

    if (error.response?.data?.errors) {
      const errors = error.response.data.errors as Record<string, string[]>;
      const errorValues = Object.values(errors);
      const firstErrorArray = errorValues[0];
      const firstError = Array.isArray(firstErrorArray) ? firstErrorArray[0] : undefined;
      throw new Error(firstError || 'Validation failed');
    }

    if (error.response?.data?.message) {
      throw new Error(error.response.data.message);
    }

    throw error;
  }
};

// User Login
export const loginUser = async (email: string, password: string): Promise<LoginResponse> => {
  try {
    console.log('📝 Logging in:', email);

    const response = await api.post<LoginResponse>('/login', { email, password });

    if (response.data.token) {
      await AsyncStorage.setItem('token', response.data.token);
    }

    return response.data;
  } catch (error: any) {
    console.error('❌ Login error:', error.response?.data?.message || error.message);
    throw error;
  }
};

// User Logout
export const logoutUser = async (): Promise<void> => {
  try {
    await api.post('/logout');
    await AsyncStorage.removeItem('token');
  } catch (error: any) {
    console.error('❌ Logout error:', error.message);
    await AsyncStorage.removeItem('token');
    throw error;
  }
};

// Get current authenticated user
export const getCurrentUser = async (): Promise<User> => {
  try {
    const response = await api.get<User>('/me');
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching user:', error.message);
    throw error;
  }
};

// Fetch hygiene articles
export const getHygieneArticles = async (category?: string): Promise<HygieneArticle[]> => {
  try {
    const url = category
      ? `/hygiene-articles?category=${encodeURIComponent(category)}`
      : '/hygiene-articles';
    const response = await api.get<HygieneArticle[]>(url);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching articles:', error.message);
    throw error;
  }
};

// Fetch single article
export const getSingleArticle = async (id: number): Promise<HygieneArticle> => {
  try {
    const response = await api.get<HygieneArticle>(`/hygiene-articles/${id}`);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching article:', error.message);
    throw error;
  }
};

// Fetch emergency contacts
export const getEmergencyContacts = async (): Promise<EmergencyContact[]> => {
  try {
    const response = await api.get<EmergencyContact[]>('/emergency-contacts');
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching contacts:', error.message);
    throw error;
  }
};

// ============================================
// MENTOR API FUNCTIONS
// ============================================

export const getActiveMentors = async (search?: string, expertise?: string): Promise<Mentor[]> => {
  try {
    // Use the dedicated endpoint for active mentors
    let url = '/available-mentors';
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (expertise) params.append('expertise', expertise);
    if (params.toString()) url += `?${params.toString()}`;

    console.log('🔍 getActiveMentors: Fetching from URL:', url);
    const response = await api.get(url);
    console.log('📥 getActiveMentors: Response status:', response.status);

    let mentorsArray: any[] = [];

    // Your API returns: { success: true, message: "...", mentors: [...], total: X }
    if (response.data && response.data.success === true && Array.isArray(response.data.mentors)) {
      mentorsArray = response.data.mentors;
      console.log('✅ Extracted mentors from response.data.mentors, count:', mentorsArray.length);
    }
    // Fallback for direct array
    else if (Array.isArray(response.data)) {
      mentorsArray = response.data;
      console.log('✅ Response is direct array, count:', mentorsArray.length);
    }
    // Fallback for data wrapper
    else if (response.data && response.data.data && Array.isArray(response.data.data)) {
      mentorsArray = response.data.data;
      console.log('✅ Extracted mentors from response.data.data, count:', mentorsArray.length);
    }
    else {
      console.warn('⚠️ Unknown response format:', response.data);
      return [];
    }

    console.log(`📊 Found ${mentorsArray.length} mentors from API`);

    // Transform each mentor to match the Mentor interface
    const transformedMentors: Mentor[] = mentorsArray.map((mentor: any) => {
      // Handle expertise - could be array or null
      let expertiseArray: string[] = [];
      if (mentor.expertise) {
        if (Array.isArray(mentor.expertise)) {
          expertiseArray = mentor.expertise;
        } else if (typeof mentor.expertise === 'string') {
          try {
            const parsed = JSON.parse(mentor.expertise);
            expertiseArray = Array.isArray(parsed) ? parsed : [mentor.expertise];
          } catch (e) {
            expertiseArray = [mentor.expertise];
          }
        }
      }

      // Handle available_days
      let availableDaysArray: string[] = [];
      if (mentor.available_days) {
        if (Array.isArray(mentor.available_days)) {
          availableDaysArray = mentor.available_days;
        } else if (typeof mentor.available_days === 'string') {
          try {
            const parsed = JSON.parse(mentor.available_days);
            availableDaysArray = Array.isArray(parsed) ? parsed : [];
          } catch (e) {
            availableDaysArray = [];
          }
        }
      }

      // Determine status - default to 'active' if not specified
      const status = mentor.status || 'active';

      return {
        id: mentor.id,
        name: mentor.name || 'Unknown',
        email: mentor.email || '',
        phone: mentor.phone || null,
        location: mentor.location || null,
        photo: mentor.photo || null,
        avatar: mentor.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name || 'Mentor')}&background=8b5cf6&color=fff`,
        expertise: expertiseArray,
        bio: mentor.bio || '',
        availability: mentor.availability || null,
        available_days: availableDaysArray,
        available_time_start: mentor.available_time_start || null,
        available_time_end: mentor.available_time_end || null,
        linkedin_url: mentor.linkedin_url || null,
        twitter_url: mentor.twitter_url || null,
        website_url: mentor.website_url || null,
        // ✅ FIX: API returns 'average_rating', not 'rating'
        average_rating: mentor.average_rating ?? null,
        rating: mentor.average_rating ?? mentor.rating ?? null,
        total_sessions: mentor.total_sessions || 0,
        status: status,
        created_at: mentor.created_at,
        updated_at: mentor.updated_at,
      };
    });

    console.log(`✅ Transformed ${transformedMentors.length} active mentors`);
    if (transformedMentors.length > 0) {
      console.log('📝 First mentor:', transformedMentors[0].name, 'Rating:', transformedMentors[0].average_rating);
    } else {
      console.warn('⚠️ No active mentors found. Make sure mentors have status="active" in database');
    }

    return transformedMentors;
  } catch (error: any) {
    console.error('❌ Error fetching active mentors:', error.message);
    if (error.response) {
      console.error('Response status:', error.response.status);
      console.error('Response data:', error.response.data);
    }
    return [];
  }
};

/**
 * Get single mentor details by ID
 */
export const getMentorDetails = async (mentorId: number): Promise<Mentor | null> => {
  try {
    const response = await api.get(`/mentors/${mentorId}`);
    console.log('📥 Raw mentor details response:', response.data);

    let mentor = null;

    // Extract mentor from response
    if (response.data && response.data.success === true && response.data.mentor) {
      mentor = response.data.mentor;
    } else if (response.data && response.data.mentor) {
      mentor = response.data.mentor;
    } else if (response.data && typeof response.data === 'object' && !Array.isArray(response.data)) {
      mentor = response.data;
    }

    if (!mentor) {
      console.log('⚠️ No mentor found in response');
      return null;
    }

    // Handle expertise parsing
    let expertiseArray: string[] = mentor.expertise || [];
    if (typeof expertiseArray === 'string') {
      try {
        const parsed = JSON.parse(expertiseArray);
        expertiseArray = Array.isArray(parsed) ? parsed : [];
      } catch (e) {
        expertiseArray = [];
      }
    }

    // Parse available days if it's a string
    let availableDays = mentor.available_days;
    if (typeof availableDays === 'string') {
      try {
        availableDays = JSON.parse(availableDays);
      } catch (e) {
        availableDays = [];
      }
    }

    return {
      ...mentor,
      expertise: expertiseArray,
      available_days: availableDays,
      // ✅ FIX: map average_rating correctly
      average_rating: mentor.average_rating ?? null,
      rating: mentor.average_rating ?? mentor.rating ?? null,
      avatar: mentor.photo || mentor.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=874179&color=fff`,
    };
  } catch (error: any) {
    console.error('❌ Error fetching mentor details:', error.message);
    return null;
  }
};

/**
 * Get all mentors (including inactive/pending) - for admin use only
 * This requires authentication
 */
export const getAllMentors = async (): Promise<Mentor[]> => {
  try {
    const response = await api.get('/mentors');
    console.log('📥 Raw all mentors response:', response.data);

    let mentorsArray = [];

    if (response.data && response.data.success === true && response.data.mentors) {
      mentorsArray = response.data.mentors;
    } else if (Array.isArray(response.data)) {
      mentorsArray = response.data;
    } else if (response.data && response.data.data && Array.isArray(response.data.data)) {
      mentorsArray = response.data.data;
    }

    return mentorsArray;
  } catch (error: any) {
    console.error('❌ Error fetching all mentors:', error.message);
    return [];
  }
};

// Legacy function - kept for backward compatibility
export const getMentor = async (): Promise<Mentor[]> => {
  return getActiveMentors();
};

// ============================================
// CHAT & MESSAGING FUNCTIONS
// ============================================

// Fetch chat list
export const getChatList = async (): Promise<ChatListItem[]> => {
  try {
    const response = await api.get('/conversations');
    console.log("CHAT DATA SAMPLE:", JSON.stringify(response.data?.[0], null, 2));

    // Handle wrapped response
    if (response.data?.data && Array.isArray(response.data.data)) {
      return response.data.data;
    }

    if (Array.isArray(response.data)) {
      return response.data;
    }

    return [];
  } catch (error: any) {
    console.error('❌ Error fetching chats:', error.message);
    throw error;
  }
};

// Send message
export const sendMessage = async (
  conversationId: number,
  message: string,
  token?: string
): Promise<Message> => {
  try {
    const response = await api.post<Message>('/messages', {
      conversation_id: conversationId,
      message: message
    }, token ? { headers: { Authorization: `Bearer ${token}` } } : undefined);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error sending message:', error.message);
    throw error;
  }
};

// Create conversation
export const createConversation = async (payload: {
  mentor_id?: number;
  receiver_id?: number;
  target_user_id?: number;
  initial_message?: string;
}): Promise<Conversation> => {
  try {
    const target_user_id = payload.target_user_id ?? payload.mentor_id ?? payload.receiver_id;
    const response = await api.post<Conversation>('/conversations', {
      ...payload,
      target_user_id,
      is_group: false,
    });
    return response.data;
  } catch (error: any) {
    console.error('❌ Error creating conversation:', error.message);
    throw error;
  }
};

// Get messages
export const getMessages = async (conversationId: number, token?: string): Promise<Message[]> => {
  try {
    const response = await api.get<Message[]>(`/conversations/${conversationId}/messages`, token ? { headers: { Authorization: `Bearer ${token}` } } : undefined);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching messages:', error.message);
    throw error;
  }
};

// ============================================
// HARASSMENT REPORT FUNCTIONS
// ============================================

export const getMyReports = async (): Promise<HarassmentReport[]> => {
  try {
    const token = await AsyncStorage.getItem('token');
    const response = await api.get('/harassment-reports/my-reports', {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
    const payload = response.data;
    if (payload?.success && Array.isArray(payload.data)) return payload.data;
    if (Array.isArray(payload)) return payload;
    return [];
  } catch (error: any) {
    console.error('❌ Error fetching my reports:', error.message);
    throw error;
  }
};

/** @deprecated use getMyReports instead */
export const getReportHarassment = getMyReports;

export const submitHarassmentReport = async (data: {
  incident_type: string;
  incident_title: string;
  incident_description: string;
  incident_location: string;
  incident_date: string;
  perpetrator_info?: string | null;
  is_anonymous: boolean;
  victim_name?: string;
  victim_email?: string;
  victim_phone?: string;
}): Promise<any> => {
  try {
    const requestData: any = {
      incident_type: data.incident_type,
      incident_title: data.incident_title,
      incident_description: data.incident_description,
      incident_location: data.incident_location,
      incident_date: data.incident_date,
      perpetrator_info: data.perpetrator_info || null,
      is_anonymous: data.is_anonymous ? 1 : 0,
    };

    if (!data.is_anonymous) {
      requestData.victim_name = data.victim_name;
      requestData.victim_email = data.victim_email;
      requestData.victim_phone = data.victim_phone || null;
    }

    console.log('📤 Submitting harassment report:', JSON.stringify(requestData, null, 2));
    const token = await AsyncStorage.getItem('token');
    const response = await api.post('/harassment-reports', requestData, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
    console.log('✅ Report submitted successfully:', response.data);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error submitting report:', error.message);
    if (error.response?.data?.errors) {
      console.error('Validation errors:', JSON.stringify(error.response.data.errors, null, 2));
    }
    if (error.response?.data?.message) {
      console.error('Server message:', error.response.data.message);
    }
    throw error;
  }
};

export const submitAnonymousReport = async (data: {
  title: string;
  description: string;
  location?: string;
}): Promise<any> => {
  try {
    const requestData = {
      incident_type: 'other',
      incident_title: data.title,
      incident_description: data.description,
      incident_location: data.location || 'Not specified',
      incident_date: new Date().toISOString().split('T')[0],
      perpetrator_info: null,
      is_anonymous: 1,
    };

    console.log('📤 Submitting anonymous report:', JSON.stringify(requestData, null, 2));
    const response = await api.post('/harassment-reports', requestData);
    console.log('✅ Anonymous report submitted successfully:', response.data);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error submitting anonymous report:', error.message);
    console.error("Status:", error.response?.status);
    console.error("Data:", JSON.stringify(error.response?.data, null, 2));
    console.error("Headers:", error.response?.headers);
    console.error("Message:", error.message);
    throw error;
  }
};

export const getReportByReference = async (referenceNumber: string): Promise<ReportTracking | null> => {
  try {
    const response = await api.get(`/harassment-reports/reference/${encodeURIComponent(referenceNumber)}`);
    const payload = response.data;
    if (payload?.success && payload.data) return payload.data as ReportTracking;
    return null;
  } catch (error: any) {
    console.error('❌ Error fetching report by reference:', error.message);
    return null;
  }
};

// ============================================
// NOTIFICATIONS API
// ============================================


export const getNotifications = async (): Promise<{
  notifications: AppNotification[];
  unread_count: number;
}> => {
  try {
    const token = await AsyncStorage.getItem('token');
    const response = await api.get('/notifications', {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });

    const payload = response.data;

    
    let items: AppNotification[] = [];

    if (Array.isArray(payload?.notifications)) {
      items = payload.notifications;                  // ← what the fixed controller sends
    } else if (Array.isArray(payload?.data?.data)) {
      items = payload.data.data;                      // Laravel paginated (old shape)
    } else if (Array.isArray(payload?.data)) {
      items = payload.data;                           
    } else if (Array.isArray(payload)) {
      items = payload;                             
    } else {
      console.warn('⚠️ Could not resolve notifications array from:', Object.keys(payload ?? {}));
    }

    

    return {
      notifications: items,
      unread_count: payload?.unread_count ?? items.filter((n) => !n.is_read).length,
    };
  } catch (error: any) {
    console.error('❌ Error fetching notifications:', error.message);
    throw error;
  }
};

export const markNotificationRead = async (id: number): Promise<void> => {
  try {
    const token = await AsyncStorage.getItem('token');
    // PATCH /notifications/{id}/read
    await api.patch(`/notifications/${id}/read`, {}, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
  } catch (error: any) {
    console.error('❌ Error marking notification read:', error.message);
    throw error;
  }
};

export const markAllNotificationsRead = async (): Promise<void> => {
  try {
    const token = await AsyncStorage.getItem('token');
    // PATCH /notifications/read-all
    await api.patch('/notifications/read-all', {}, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
  } catch (error: any) {
    console.error('❌ Error marking all notifications read:', error.message);
    throw error;
  }
};

// Fetch general guides
export const getGeneralGuides = async (): Promise<any[]> => {
  try {
    const response = await api.get('/general-guides');
    return response.data;
  } catch (error: any) {
    console.error('❌ Error fetching guides:', error.message);
    throw error;
  }
};

// ============================================
// GUIDANCE CONTENT API
// ============================================

const buildGuidanceFormData = (payload: GuidanceContentPayload, method?: 'PUT'): FormData => {
  const formData = new FormData();
  formData.append('title', payload.title);
  formData.append('body', payload.body);
  formData.append('category', payload.category);
  formData.append('status', payload.status ?? 'published');

  if (payload.photo?.uri) {
    const uri = payload.photo.uri;
    const name = payload.photo.name ?? uri.split('/').pop() ?? 'photo.jpg';
    const type = payload.photo.type ?? 'image/jpeg';
    formData.append('photo', { uri, name, type } as unknown as Blob);
  }

  if (payload.remove_photo) {
    formData.append('remove_photo', '1');
  }

  if (method) {
    formData.append('_method', method);
  }

  return formData;
};

const guidanceMultipartConfig = {
  headers: {
    Accept: 'application/json',
    'Content-Type': 'multipart/form-data',
  },
  transformRequest: (data: FormData) => data,
};

export const getPublishedGuidanceContent = async (
  category: 'menstrual_hygiene' | 'general'
): Promise<GuidanceContent[]> => {
  try {
    const response = await api.get(`/content?category=${category}`);
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error fetching guidance content:', error.message);
    throw error;
  }
};

export const getGuidanceContentDetail = async (id: number): Promise<GuidanceContent> => {
  try {
    const response = await api.get(`/content/${id}`);
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error fetching guidance detail:', error.message);
    throw error;
  }
};

export const getMentorGuidanceContent = async (): Promise<GuidanceContent[]> => {
  try {
    const response = await api.get('/mentor/content');
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error fetching mentor content:', error.message);
    throw error;
  }
};

export const publishGuidanceContent = async (
  payload: GuidanceContentPayload
): Promise<GuidanceContent> => {
  try {
    const hasPhoto = Boolean(payload.photo?.uri);
    const response = hasPhoto
      ? await api.post(
          '/mentor/content',
          buildGuidanceFormData(payload),
          guidanceMultipartConfig
        )
      : await api.post('/mentor/content', {
          ...payload,
          status: payload.status ?? 'published',
        });
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error publishing content:', error.message);
    throw error;
  }
};

export const updateGuidanceContent = async (
  id: number,
  payload: GuidanceContentPayload
): Promise<GuidanceContent> => {
  try {
    const hasPhoto = Boolean(payload.photo?.uri);
    const hasRemove = Boolean(payload.remove_photo);

    if (hasPhoto || hasRemove) {
      const response = await api.post(
        `/mentor/content/${id}`,
        buildGuidanceFormData(payload, 'PUT'),
        guidanceMultipartConfig
      );
      return response.data.data ?? response.data;
    }

    const response = await api.put(`/mentor/content/${id}`, payload);
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error updating content:', error.message);
    throw error;
  }
};

export const toggleGuidanceContentStatus = async (id: number): Promise<GuidanceContent> => {
  try {
    const response = await api.patch(`/mentor/content/${id}/unpublish`);
    return response.data.data ?? response.data;
  } catch (error: any) {
    console.error('❌ Error toggling content status:', error.message);
    throw error;
  }
};

export const deleteGuidanceContent = async (id: number): Promise<void> => {
  try {
    await api.delete(`/mentor/content/${id}`);
  } catch (error: any) {
    console.error('❌ Error deleting content:', error.message);
    throw error;
  }
};

// ============================================
// USER & ADMIN FUNCTIONS
// ============================================

export const getAllUsers = async (token: string) => {
  try {
    const response = await api.get("/users", {
      headers: { Authorization: `Bearer ${token}` },
    });
    return response.data;
  } catch (error) {
    console.error("❌ Fetch Users Error:", error);
    throw error;
  }
};

export const getConversation = async (conversationId: number, token?: string) => {
  try {
    const response = await api.get(`/conversations/${conversationId}`, token ? {
      headers: { Authorization: `Bearer ${token}` },
    } : undefined);
    return response.data;
  } catch (error) {
    console.error("❌ Error fetching conversation:", error);
    throw error;
  }
};

export const getUser = async (userId: number, token: string) => {
  try {
    const response = await api.get(`users/${userId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    return response.data;
  } catch (error) {
    console.error("❌ Error fetching user:", error);
    throw error;
  }
};

export const sendMentorshipRequest = async (
  data: MentorshipRequestData,
  token: string
) => {
  const response = await api.post('/mentorship/request', data, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
};

export const getMentorshipSessions = async (token: string): Promise<{
  outgoing: MentorshipSession[];
  incoming: MentorshipSession[];
}> => {
  const response = await api.get('/mentorship/my-sessions', {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
};

export const getMentorSessions = async (token: string): Promise<MentorshipSession[]> => {
  const response = await api.get('/mentorship/mentor-sessions', {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
};

export const updateSessionStatus = async (
  sessionId: number | string,
  token: string,
  payload: {
    status: 'accepted' | 'declined' | 'completed';
    mentor_notes?: string;
    scheduled_at?: string;
  }
) => {
  try {
    const response = await api.patch(
      `/mentorship/sessions/${sessionId}/status`,
      payload,
      { headers: { Authorization: `Bearer ${token}` } }
    );
    return response.data;
  } catch (error: any) {
    console.error('❌ updateSessionStatus error:', error.response?.data || error.message);
    throw error.response?.data || error;
  }
};

export const startMentorshipConversation = async (
  sessionId: number | string,
  token: string
): Promise<{ message: string; conversation: Conversation }> => {
  try {
    const response = await api.post(
      `/mentorship/sessions/${sessionId}/start`,
      {},
      { headers: { Authorization: `Bearer ${token}` } }
    );
    return response.data;
  } catch (error: any) {
    console.error('❌ startMentorshipConversation error:', error.response?.data || error.message);
    throw error.response?.data || error;
  }
};

export const submitSessionReview = async (
  sessionId: number | string,
  token: string,
  payload: { rating: number; comment?: string }
): Promise<{ message: string; review: MentorReview }> => {
  try {
    const response = await api.post(
      `/mentorship/sessions/${sessionId}/review`,
      payload,
      { headers: { Authorization: `Bearer ${token}` } }
    );
    return response.data;
  } catch (error: any) {
    console.error('❌ submitSessionReview error:', error.response?.data || error.message);
    throw error.response?.data || error;
  }
};

export const terminateMentorshipSession = async (
  sessionId: number | string,
  token: string,
  mentorNotes?: string
) => {
  const response = await api.post(
    `/mentorship/sessions/${sessionId}/terminate`,
    {
      mentor_notes: mentorNotes,
    },
    { headers: { Authorization: `Bearer ${token}` } }
  );

  return response.data;
};

export const getMentorReviews = async (
  mentorId: number
): Promise<{ average_rating: number; total: number; reviews: MentorReview[] }> => {
  try {
    const response = await api.get(`/mentors/${mentorId}/reviews`);
    return response.data;
  } catch (error: any) {
    console.error('❌ getMentorReviews error:', error.message);
    throw error;
  }
};

export const getGroups = async (token: string) => {
  try {
    const response = await api.get('/groups/available', {
      headers: { Authorization: `Bearer ${token}` }
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching groups:", error);
    throw error;
  }
};

export const joinGroup = async (token: string, conversationID: number) => {
  try {
    const response = await api.post(`/conversations/${conversationID}/join`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
    return response.data;
  } catch (error) {
    console.error("Error joining group:", error);
    throw error;
  }
};

export const testConnection = async (): Promise<boolean> => {
  try {
    console.log('🔍 Testing connection to:', api.defaults.baseURL);
    const response = await api.get('/mentors/active', {
      validateStatus: (status) => status < 500
    });
    console.log('Connection successful! Server responded with status:', response.status);
    return true;
  } catch (error: any) {
    console.error(' Connection failed:', error.message);
    return false;
  }
};

export const inteligencyRequest = async (query: string) => {
  try {
    const response = await api.post("/ask", {
      content: query
    });
    console.log(response.data)
    return response.data;
  } catch (error: any) {
    console.error("Error fetching intelligence predictions:", error?.response?.data || error.message);
    return null;
  }
};

export const getCommunityPosts = async (): Promise<CommunityPost[]> => {
  try {
    const response = await api.get('/community/posts');
    return response.data.posts;
  } catch (error: any) {
    console.error('❌ getCommunityPosts error:', error.message);
    return [];
  }
};

export const createCommunityPost = async (data: {
  text: string;
  category: string;
}) => {
  try {
    const response = await api.post('/community/posts', data);
    return response.data.post;
  } catch (error: any) {
    console.error('❌ createCommunityPost error:', error.message);
    throw error;
  }
};

export const likeCommunityPost = async (postId: number) => {
  try {
    const response = await api.post(`/community/posts/${postId}/like`);
    return response.data;
  } catch (error: any) {
    console.error('❌ likeCommunityPost error:', error.message);
    throw error;
  }
};

export const commentCommunityPost = async (
  postId: number,
  comment: string
) => {
  try {
    const response = await api.post(
      `/community/posts/${postId}/comments`,
      { comment }
    );
    return response.data.comment;
  } catch (error: any) {
    console.error('❌ commentCommunityPost error:', error.message);
    throw error;
  }
};

export default api;
