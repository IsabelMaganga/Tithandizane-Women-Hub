import axios from 'axios';


// fetching data from Laravel API
const api = axios.create({
  baseURL: 'http://192.168.43.103:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});


//handler user registration
export const registerUser = async (userData) => {
  try {
    const response = await api.post('/register', userData);
    return response.data;
  } catch (error) {
    console.error('Error registering user:', error.message);
    throw error;
  }
};

//handler user login
// export const loginUser = async (email,password) => {
//   try {
//     const response = await api.post('/login', { email:email, password: password });
//     return response.data;
//   } catch (error) {
//     console.error('Error logging in user:', error.message);
//     throw error;
//   }
// };

// Fetch hygiene articles (optional category filter)
export const getHygieneArticles = async (category) => {
  try {
    const url = category
      ? `/hygiene-articles?category=${category}`
      : '/hygiene-articles';

    const response = await api.get(url);
    return response.data;
  } catch (error) {
    console.error('Error fetching hygiene articles:', error.message);
    throw error;
  }
};


// Fetch single article
export const getSingleArticle = async (id:number) => {
  try {
    const response = await api.get(`/hygiene-articles/${id}`);
    return response.data;
  } catch (error) {
    console.error('Error fetching article:', error.message);
    throw error;
  }
};


// Fetch emergency contacts
export const getEmergencyContacts = async () => {
  try {
    const response = await api.get('/emergency-contacts');
    return response.data;
  } catch (error) {
    console.error('Error fetching emergency contacts:', error.message);
    throw error;
  }
};

export const getMentor= async ()=>{
  try {
    const response = await api.get('/mentors');
    return response.data;
  } catch (error) {
    console.log(error)

  }
}

export const getChatList = async (token: string) => {
  try {
    const response = await api.get('/conversations', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching chats:", error);
    throw error;
  }
};

export const sendMessage = async (
  conversationId: number,
  message: string,
  token: string
) => {
  try {

    const response = await api.post(
      "/messages",
      {
        conversation_id: conversationId,
        message: message
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    return response.data;

  } catch (error: any) {

    console.log("Laravel error:", error.response?.data);

    throw error;
  }
};


export const createConversation = async (payload, token:string) => {
  try {
    const response = await api.post('/conversations', payload, {
      headers: { Authorization: `Bearer ${token}` },
    });
    return response.data;
  } catch (error) {
    console.error('Error creating conversation:', error);
    throw error;
  }
};


export const getMessages = async (conversationId: number, token: string) => {
  try {
    const response = await api.get(`/conversations/${conversationId}/messages`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching messages:", error);
    throw error;
  }
};

export const getReportHarrasment= async (token:string) => {
  try {
    const response = await api.get('/harassment-reports/my-reports',{
      headers:{
        Authorization: `Bearer ${token}`
      }
    })
    
  } catch (error) {
    console.log("Error fetching messages")
    
  }
  
}


export const submitHarassmentReportAnnonymously = async (data:any) => {
  try {
    const response =  await api.post('/harassment-reports/anonymous');
  } catch (error) {
    console.log("Error reporting anonnymously")
  }
}

// Submit authenticated report
export const submitHarassmentReport = async (data: any) => {
  const { token, ...body } = data;
  return await api.post("/harassment-reports", body, {
    headers: { Authorization: `Bearer ${token}` },
  });
};

// Submit anonymous report (public)
export const submitAnonymousReport = async (data: any) => {
  return await api.post("/harassment-reports/anonymous", data);
};


export const getGeneralGuides= async ()=>{
  try {
    const response = await api.get("/general-guides");
    return response.data;
  } catch (error) {
    
  }
}


export default api;