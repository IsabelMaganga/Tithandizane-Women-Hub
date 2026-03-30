// services/api.ts

import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

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
  expertise: string;
  bio?: string;
  avatar_url?: string;
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
  title: string;
  description: string;
  location?: string;
  status: 'pending' | 'investigating' | 'resolved';
  created_at: string;
}

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: 'http://192.168.43.103:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 15000,
});

// Request interceptor to add auth token
api.interceptors.request.use(
  async (config: InternalAxiosRequestConfig): Promise<InternalAxiosRequestConfig> => {
    const token = await AsyncStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error: AxiosError) => {
    console.error('Request interceptor error:', error.message);
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    // Handle 401 Unauthorized errors
    if (error.response?.status === 401) {
      console.log('Unauthorized access - clearing token');
      await AsyncStorage.removeItem('token');
      // You can add navigation to login here if needed
    }
    
    // Log detailed error information
    if (error.response) {
      console.error('API Error Response:', {
        status: error.response.status,
        data: error.response.data,
        headers: error.response.headers,
      });
    } else if (error.request) {
      console.error('API Error Request (no response):', error.request);
    } else {
      console.error('API Error Message:', error.message);
    }
    
    return Promise.reject(error);
  }
);

// User Registration
export const registerUser = async (userData: RegisterData): Promise<LoginResponse> => {
  try {
    const response = await api.post<LoginResponse>('/register', userData);
    return response.data;
  } catch (error: any) {
    console.error('Error registering user:', error.response?.data?.message || error.message);
    
    // Throw validation errors for better handling
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors as Record<string, string[]>;
      const errorValues = Object.values(errors);
      const firstErrorArray = errorValues[0];
      const firstError = Array.isArray(firstErrorArray) ? firstErrorArray[0] : firstErrorArray;
      throw new Error(firstError || 'Validation failed');
    }
    
    throw error;
  }
};

// User Login
export const loginUser = async (email: string, password: string): Promise<LoginResponse> => {
  try {
    const response = await api.post<LoginResponse>('/login', { email, password });
    return response.data;
  } catch (error: any) {
    console.error('Error logging in user:', error.response?.data?.message || error.message);
    throw error;
  }
};

// User Logout
export const logoutUser = async (): Promise<void> => {
  try {
    await api.post('/logout');
  } catch (error: any) {
    console.error('Error logging out:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Get current authenticated user
export const getCurrentUser = async (): Promise<User> => {
  try {
    const response = await api.get<User>('/me');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching current user:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Fetch hygiene articles (optional category filter)
export const getHygieneArticles = async (category?: string): Promise<HygieneArticle[]> => {
  try {
    const url = category
      ? `/hygiene-articles?category=${encodeURIComponent(category)}`
      : '/hygiene-articles';
    const response = await api.get<HygieneArticle[]>(url);
    return response.data;
  } catch (error: any) {
    console.error('Error fetching hygiene articles:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Fetch single article
export const getSingleArticle = async (id: number): Promise<HygieneArticle> => {
  try {
    const response = await api.get<HygieneArticle>(`/hygiene-articles/${id}`);
    return response.data;
  } catch (error: any) {
    console.error('Error fetching article:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Fetch emergency contacts
export const getEmergencyContacts = async (): Promise<EmergencyContact[]> => {
  try {
    const response = await api.get<EmergencyContact[]>('/emergency-contacts');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching emergency contacts:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Fetch mentors
export const getMentor = async (): Promise<Mentor[]> => {
  try {
    const response = await api.get<Mentor[]>('/mentors');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching mentors:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Fetch chat list (conversations)
export const getChatList = async (): Promise<Conversation[]> => {
  try {
    const response = await api.get<Conversation[]>('/conversations');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching chats:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Send message
export const sendMessage = async (
  conversationId: number,
  message: string
): Promise<Message> => {
  try {
    const response = await api.post<Message>('/messages', {
      conversation_id: conversationId,
      message: message
    });
    return response.data;
  } catch (error: any) {
    console.error('Error sending message:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Create conversation
export const createConversation = async (payload: {
  mentor_id: number;
  initial_message?: string;
}): Promise<Conversation> => {
  try {
    const response = await api.post<Conversation>('/conversations', payload);
    return response.data;
  } catch (error: any) {
    console.error('Error creating conversation:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Get messages for a conversation
export const getMessages = async (conversationId: number): Promise<Message[]> => {
  try {
    const response = await api.get<Message[]>(`/conversations/${conversationId}/messages`);
    return response.data;
  } catch (error: any) {
    console.error('Error fetching messages:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Get user's harassment reports
export const getReportHarassment = async (): Promise<HarassmentReport[]> => {
  try {
    const response = await api.get<HarassmentReport[]>('/harassment-reports/my-reports');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching harassment reports:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Submit harassment report (authenticated)
export const submitHarassmentReport = async (data: {
  title: string;
  description: string;
  location?: string;
}): Promise<HarassmentReport> => {
  try {
    const response = await api.post<HarassmentReport>('/harassment-reports', data);
    return response.data;
  } catch (error: any) {
    console.error('Error submitting harassment report:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Submit anonymous harassment report
export const submitHarassmentReportAnonymously = async (data: {
  title: string;
  description: string;
  location?: string;
}): Promise<HarassmentReport> => {
  try {
    const response = await api.post<HarassmentReport>('/harassment-reports/anonymous', data);
    return response.data;
  } catch (error: any) {
    console.error('Error reporting anonymously:', error.response?.data?.message || error.message);
    throw error;
  }
};

// Submit anonymous report (alias)
export const submitAnonymousReport = async (data: {
  title: string;
  description: string;
  location?: string;
}): Promise<HarassmentReport> => {
  return submitHarassmentReportAnonymously(data);
};

// Fetch general guides
export const getGeneralGuides = async (): Promise<any[]> => {
  try {
    const response = await api.get('/general-guides');
    return response.data;
  } catch (error: any) {
    console.error('Error fetching general guides:', error.response?.data?.message || error.message);
    throw error;
  }
};

//get all users except mentor and admins
export const getAllUsers = async (token: string) => {
  try {
    const response = await api.get("/users", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    return response.data;
  } catch (error) {
    console.error("Fetch Users Error:", error);
    throw error;
  }
};


export const getConversation = async (
        conversationId: number,
        token: string
      ) => {
        try {
          const response = await api.get(`/conversations/${conversationId}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });

          return response.data;
        } catch (error) {
          console.error("Error fetching conversation:", error);
          throw error;
        }
};


export const getUser = async (userId:number,token:string) => {
   try {
    const response = await api.get(`users/${userId}`,{
      headers:{
        Authorization:`Bearer ${token}`
      }
    })
    return response.data;
    
   } catch (error) {
    
   }
}

//requesting the mentorship session
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

//getting all sessions
export const getMentorshipSessions = async (token: string) => {
  const response = await api.get('/mentorship/my-sessions', {
    headers: { Authorization: `Bearer ${token}` }
  });
  return response.data;
};

//updating mentorship session by mentors
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
    // Ensure the URL matches: /mentorship/sessions/{session}
    const response = await api.patch(`/mentorship/sessions/${sessionId}`, payload, {
      headers: { Authorization: `Bearer ${token}` },
    });
    return response.data;
  } catch (error: any) {
    console.error("API Error:", error.response?.data || error.message);
    throw error.response?.data || error;
  }
};
//group finding an joining
export const getGroups = async (token:string)=>{
  try {
    const response = await api.get('/groups/available',{
      headers:{ Authorization:`Bearer ${token}`}
    })
    return response.data;
  } catch (error) {

  }
}

export const joinGroup = async (token:string,conversationID:number) => {
  try {
    const response = await api.post(`/conversations/${conversationID}/join`,{
      headers:{ Authorization:`Bearer ${token}`}
    })
    return response.data
  } catch (error) {

  }
};

// Export the api instance for use in contexts
export default api;