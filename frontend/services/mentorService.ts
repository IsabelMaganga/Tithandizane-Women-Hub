import {
  getActiveMentors,
  getMentorDetails,
  getMentor,
} from './api';

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
}

class MentorService {
  async getActiveMentors(search?: string, expertise?: string): Promise<Mentor[]> {
    try {
      return await getActiveMentors(search, expertise);
    } catch (error) {
      console.error('Failed to load mentors:', error);
      return [];
    }
  }

  async getMentorDetails(id: number): Promise<Mentor | null> {
    try {
      return await getMentorDetails(id);
    } catch (error) {
      console.error('Failed to load mentor details:', error);
      return null;
    }
  }

  getExpertiseAreas(mentors: Mentor[]): string[] {
    const expertiseSet = new Set<string>();
    mentors.forEach(mentor => {
      if (mentor.expertise && Array.isArray(mentor.expertise)) {
        mentor.expertise.forEach(exp => {
          if (exp && exp.trim()) {
            expertiseSet.add(exp.trim());
          }
        });
      }
    });
    return Array.from(expertiseSet).sort();
  }

  filterMentorsBySearch(mentors: Mentor[], searchTerm: string): Mentor[] {
    if (!searchTerm.trim()) return mentors;
    
    const term = searchTerm.toLowerCase();
    return mentors.filter(mentor =>
      mentor.name.toLowerCase().includes(term) ||
      (mentor.bio && mentor.bio.toLowerCase().includes(term)) ||
      (mentor.expertise && mentor.expertise.some(exp => exp.toLowerCase().includes(term)))
    );
  }

  filterMentorsByExpertise(mentors: Mentor[], expertise: string): Mentor[] {
    if (!expertise) return mentors;
    
    return mentors.filter(mentor =>
      mentor.expertise && mentor.expertise.includes(expertise)
    );
  }
}

export const mentorService = new MentorService();