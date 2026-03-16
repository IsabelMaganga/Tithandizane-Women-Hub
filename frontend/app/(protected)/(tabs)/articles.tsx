import React, { useEffect, useState, useMemo, useCallback } from 'react';
import { 
  View, 
  Text, 
  Pressable, 
  ScrollView, 
  Image, 
  TextInput, 
  KeyboardAvoidingView, 
  Platform 
} from 'react-native';
import { useRouter } from 'expo-router';
import { getHygieneArticles, getGeneralGuides } from '../../../services/api';
import { LegendList } from '@legendapp/list';
import LottieView from 'lottie-react-native';
import { Feather } from '@expo/vector-icons';

// --- Types ---
type Article = {
  id: number;
  title: string;
  content: string;
  category: string;
  image_url: string;
};

type SourceType = 'hygiene' | 'general';

const ArticlesScreen = () => {
  const router = useRouter();
  
  // --- State ---
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState<string[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [activeSource, setActiveSource] = useState<SourceType>('hygiene');
  const [searchQuery, setSearchQuery] = useState('');

  // --- Data Fetching ---
  const fetchArticles = useCallback(async (source: SourceType) => {
    try {
      setLoading(true);
      // Reset filters when switching sources for a clean state
      setSelectedCategory(null);
      setSearchQuery('');
      
      const data = source === 'hygiene' 
        ? await getHygieneArticles() 
        : await getGeneralGuides();
      
      const safeData = Array.isArray(data) ? data : [];
      setArticles(safeData);

      // Extract unique categories from the new data
      const uniqueCategories = [
        ...new Set(safeData.map((article: Article) => article.category).filter(Boolean)),
      ];
      setCategories(uniqueCategories as string[]);
    } catch (error) {
      console.error(`Failed to fetch ${source} articles:`, error);
      setArticles([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchArticles(activeSource);
  }, [activeSource, fetchArticles]);

  // --- Combined Filtering Logic ---
  const filteredArticles = useMemo(() => {
    return articles.filter((article) => {
      const matchesCategory = !selectedCategory || 
        article?.category?.toLowerCase() === selectedCategory.toLowerCase();
      
      const matchesSearch = !searchQuery || 
        article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        article.content.toLowerCase().includes(searchQuery.toLowerCase());

      return matchesCategory && matchesSearch;
    });
  }, [articles, selectedCategory, searchQuery]);

  // --- Handlers ---
  const handlePressArticle = (id: number) => {
    router.push(`/articles/${id}`);
  };

  // --- Loading State ---
  if (loading && articles.length === 0) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width: 180, height: 180 }}
        />
        <Text className="text-slate-400 font-medium -mt-4">Loading health tips...</Text>
      </View>
    );
  }

  return (
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
      className="flex-1 bg-slate-50"
    >
      {/* Header & Search Section */}
      <View className="bg-emerald-600 pt-14 pb-6 px-6 rounded-b-[40px] shadow-md">
        <Text className="text-white text-2xl font-bold">Health & Education</Text>
        <Text className="text-emerald-100 text-sm mt-1 mb-5">Expert advice for your wellbeing</Text>
        
        {/* Search Bar */}
        <View className="flex-row items-center bg-emerald-700/40 rounded-2xl px-4 mb-5 border border-emerald-500/30">
          <Feather name="search" size={18} color="#A7F3D0" />
          <TextInput
            placeholder="Search articles..."
            placeholderTextColor="#A7F3D0"
            className="flex-1 py-3 px-3 text-white font-medium"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
          {searchQuery.length > 0 && (
            <Pressable onPress={() => setSearchQuery('')}>
              <Feather name="x-circle" size={18} color="#A7F3D0" />
            </Pressable>
          )}
        </View>

        {/* Source Toggle Switches */}
        <View className='flex-row bg-emerald-700/30 p-1 rounded-2xl'>
          <Pressable 
            onPress={() => setActiveSource('hygiene')}
            className={`flex-1 py-2.5 rounded-xl items-center ${activeSource === 'hygiene' ? 'bg-white shadow-sm' : ''}`}
          >
            <Text className={`font-bold text-xs ${activeSource === 'hygiene' ? 'text-emerald-700' : 'text-emerald-50'}`}>
              Hygiene
            </Text>
          </Pressable>
          <Pressable 
            onPress={() => setActiveSource('general')}
            className={`flex-1 py-2.5 rounded-xl items-center ${activeSource === 'general' ? 'bg-white shadow-sm' : ''}`}
          >
            <Text className={`font-bold text-xs ${activeSource === 'general' ? 'text-emerald-700' : 'text-emerald-50'}`}>
              General Guides
            </Text>
          </Pressable>
        </View>
      </View>

      {/* Category Horizontal Filter */}
      <View className="py-4">
        <ScrollView 
          horizontal 
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={{ paddingHorizontal: 20 }}
        >
          <CategoryPill 
            label="All Topics" 
            isActive={selectedCategory === null} 
            onPress={() => setSelectedCategory(null)} 
          />
          {categories.map((cat, index) => (
            <CategoryPill 
              key={`${cat}-${index}`}
              label={cat} 
              isActive={selectedCategory === cat} 
              onPress={() => setSelectedCategory(cat)} 
            />
          ))}
        </ScrollView>
      </View>

      {/* Articles List */}
      <LegendList
        data={filteredArticles}
        estimatedItemSize={350}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={{ paddingHorizontal: 20, paddingBottom: 40 }}
        ListEmptyComponent={
          <View className="items-center mt-20 px-10">
            <Feather name="search" size={48} color="#cbd5e1" />
            <Text className="text-slate-500 mt-4 text-center font-bold text-lg">
              {searchQuery ? `No results for "${searchQuery}"` : "No articles found"}
            </Text>
            <Text className="text-slate-400 text-center mt-1">
              Try adjusting your search or switching categories.
            </Text>
          </View>
        }
        renderItem={({ item }) => (
          <ArticleCard item={item} onPress={() => handlePressArticle(item.id)} />
        )}
      />
    </KeyboardAvoidingView>
  );
};

// --- Sub-components ---

const CategoryPill = ({ label, isActive, onPress }: { label: string, isActive: boolean, onPress: () => void }) => (
  <Pressable
    onPress={onPress}
    className={`px-6 py-2.5 mr-2 rounded-2xl border ${
      isActive 
        ? 'bg-emerald-600 border-emerald-600 shadow-md shadow-emerald-200' 
        : 'bg-white border-slate-200 shadow-sm shadow-slate-50'
    }`}
  >
    <Text className={`font-bold text-xs ${isActive ? 'text-white' : 'text-slate-500'}`}>
      {label}
    </Text>
  </Pressable>
);

const ArticleCard = ({ item, onPress }: { item: Article, onPress: () => void }) => (
  <Pressable
    onPress={onPress}
    className="bg-white mb-6 rounded-[24px] overflow-hidden shadow-sm border border-slate-100 active:opacity-95"
  >
    {item.image_url ? (
      <Image
        source={{ uri: item.image_url }}
        className="w-full h-48"
        resizeMode="cover"
      />
    ) : (
      <View className="w-full h-48 bg-emerald-50 items-center justify-center">
        <Feather name="image" size={32} color="#10b981" />
      </View>
    )}

    <View className="p-5">
      <View className="bg-emerald-50 self-start px-2 py-1 rounded-md mb-2">
        <Text className="text-emerald-600 text-[10px] font-bold uppercase tracking-wider">
          {item.category}
        </Text>
      </View>

      <Text className="text-slate-900 font-bold text-lg mb-2 leading-6" numberOfLines={2}>
        {item.title}
      </Text>

      <Text className="text-slate-500 text-sm leading-5 mb-4" numberOfLines={3}>
        {item.content}
      </Text>

      <View className="flex-row items-center justify-between pt-4 border-t border-slate-50">
        <View className="flex-row items-center">
          <Feather name="clock" size={12} color="#94a3b8" />
          <Text className="text-slate-400 text-[11px] ml-1">5 min read</Text>
        </View>
        <View className="flex-row items-center">
          <Text className="text-emerald-600 font-bold text-xs mr-1">Read More</Text>
          <Feather name="chevron-right" size={14} color="#059669" />
        </View>
      </View>
    </View>
  </Pressable>
);

export default ArticlesScreen;