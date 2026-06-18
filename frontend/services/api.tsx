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
  rating?: number | null;
  total_sessions?: number;
  status?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Conversation {
  id: number;
  mentor_id: number;
  user_id: number;
  mentor?: Mentor;
  user?: User;
  last_message?: string;
  created_at: string;
}

export interface Message {
  id: number;
  conversation_id: number;
  sender_id: number;
  message: string;
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

// YOUR COMPUTER'S NETWORK IP - From Metro output: 192.168.74.205
const COMPUTER_IP = '192.168.1.132'; 
const BACKEND_PORT = '8000';

// Function to get the correct base URL based on platform
const getBaseURL = (): string => {
  if (__DEV__) {
    console.log('📱 Platform:', Platform.OS);
    
    if (Platform.OS === 'android') {
      return `http://192.168.1.132:8000/api/v1`;
    } 
    
    if (Platform.OS === 'ios') {
      return `http://192.168.74.205:8000/api`;
    }
    
    // Default fallback
    //return `http://${COMPUTER_IP}:${BACKEND_PORT}/api`;
    return `http://127.0.0.1:8000/api/v1`;
  } else {
    // Production environment
    return 'https://your-production-api.com/api';
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

/**
 * Get only ACTIVE mentors for the frontend (React Native)
 * This is the main function used by MentorshipScreen
 */
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
        rating: mentor.rating || null,
        total_sessions: mentor.total_sessions || 0,
        status: status,
        created_at: mentor.created_at,
        updated_at: mentor.updated_at,
      };
    });
    
    console.log(`✅ Transformed ${transformedMentors.length} active mentors`);
    if (transformedMentors.length > 0) {
      console.log('📝 First mentor:', transformedMentors[0].name, 'Status:', transformedMentors[0].status);
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
      avatar: mentor.photo || mentor.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=874179&color=fff`
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
export const getChatList = async (): Promise<Conversation[]> => {
  try {
    const response = await api.get<Conversation[]>('/conversations');
    return response.data;
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
// HARASSMENT REPORT FUNCTIONS - COMPLETELY FIXED
// ============================================

// Get harassment reports for the current user
// Note: uses explicit token header because the interceptor skips auth for URLs containing '/harassment-reports'
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

// Submit harassment report - Works for both anonymous and non-anonymous
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
    // Prepare data exactly as Laravel expects
    const requestData: any = {
      incident_type: data.incident_type,
      incident_title: data.incident_title,
      incident_description: data.incident_description,
      incident_location: data.incident_location,
      incident_date: data.incident_date,
      perpetrator_info: data.perpetrator_info || null,
      is_anonymous: data.is_anonymous ? 1 : 0,
    };
    
    // Only add contact info if NOT anonymous
    if (!data.is_anonymous) {
      requestData.victim_name = data.victim_name;
      requestData.victim_email = data.victim_email;
      requestData.victim_phone = data.victim_phone || null;
    }
    
    console.log('📤 Submitting harassment report:', JSON.stringify(requestData, null, 2));
    const response = await api.post('/harassment-reports', requestData);
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

// Submit anonymous report - Simplified version for anonymous reports
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
      is_anonymous: 1, // Always anonymous
    };
    
    console.log('📤 Submitting anonymous report:', JSON.stringify(requestData, null, 2));
    const response = await api.post('/harassment-reports', requestData);
    console.log('✅ Anonymous report submitted successfully:', response.data);
    return response.data;
  } catch (error: any) {
    console.error('❌ Error submitting anonymous report:', error.message);
    if (error.response?.data?.errors) {
      console.error('Validation errors:', JSON.stringify(error.response.data.errors, null, 2));
    }
    throw error;
  }
};

// Get report by reference number (public — no auth required)
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
// USER & ADMIN FUNCTIONS
// ============================================

// Get all users
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

// Get conversation
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

// Get user
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

// ============================================
// MENTORSHIP SESSION FUNCTIONS
// ============================================

// Send mentorship request
export const sendMentorshipRequest = async (data: {
  mentor_id: number;
  topic: string;
  message?: string
}, token: string) => {
  const response = await api.post('/mentorship/request', data, {
    headers: { Authorization: `Bearer ${token}` }
  });
  return response.data;
};

// Get mentorship sessions
export const getMentorshipSessions = async (token: string) => {
  const response = await api.get('/mentorship/my-sessions', {
    headers: { Authorization: `Bearer ${token}` }
  });
  return response.data;
};

// Get mentor sessions
export const getMentorSessions = async (token: string) => {
  const response = await api.get('/mentorship/mentor-sessions', {
    headers: { Authorization: `Bearer ${token}` }
  });
  return response.data;
};

// Update session status
export const updateSessionStatus = async (
  sessionId: string,
  token: string,
  payload: {
    status: 'accepted' | 'declined' | 'completed';
    mentor_notes?: string;
    scheduled_at?: string
  }
) => {
  try {
    const response = await api.patch(`/mentorship/sessions/${sessionId}`, payload, {
      headers: { Authorization: `Bearer ${token}` },
    });
    return response.data;
  } catch (error: any) {
    console.error("❌ API Error:", error.response?.data || error.message);
    throw error.response?.data || error;
  }
};

// Get groups
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

// Join group
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

// Test connection helper
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

export default api;