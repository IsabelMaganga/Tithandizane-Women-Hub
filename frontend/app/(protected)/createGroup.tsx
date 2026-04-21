import React, { useState, useMemo } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StatusBar,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { LegendList } from '@legendapp/list';
import { useNavigation } from '@react-navigation/native'; // Correct hook for navigation actions

const CONTACTS=[]

const CreateGroup = () => {
  const [groupName, setGroupName] = useState('');
  const [search, setSearch] = useState('');
  const [selectedIds, setSelectedIds] = useState(new Set());
  
  // Use navigation hook instead of route
  const navigation = useNavigation();

  const filteredContacts = useMemo(() => {
    return CONTACTS.filter(c => c.name.toLowerCase().includes(search.toLowerCase()));
  }, [search]);

  const toggleMember = (id) => {
    const newSelected = new Set(selectedIds);
    if (newSelected.has(id)) newSelected.delete(id);
    else newSelected.add(id);
    setSelectedIds(newSelected);
  };

  const renderContact = ({ item }) => {
    const isSelected = selectedIds.has(item.id);
    return (
      <TouchableOpacity 
        onPress={() => toggleMember(item.id)}
        activeOpacity={0.6}
        className="flex-row items-center h-[72px]"
      >
        <View className={`w-11 h-11 rounded-full items-center justify-center ${isSelected ? 'bg-blue-500' : 'bg-gray-200'}`}>
          <Text className={`text-base font-semibold ${isSelected ? 'text-white' : 'text-gray-900'}`}>
            {item.initial}
          </Text>
        </View>
        
        <View className="flex-1 ml-3 border-b border-gray-50 h-full justify-center">
          <Text className="text-base font-semibold text-black">{item.name}</Text>
          <Text className="text-xs text-gray-500">{item.role}</Text>
        </View>
        
        <View className={`w-6 h-6 rounded-full border-2 items-center justify-center ${isSelected ? 'bg-green-500 border-green-500' : 'border-gray-200'}`}>
          {isSelected && <Text className="text-white text-[10px] font-bold">✓</Text>}
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      <StatusBar barStyle="dark-content" />
      
      {/* Header */}
      <View className="flex-row justify-between items-center px-4 py-4 border-b border-gray-100">
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text className="text-blue-500 text-lg">Cancel</Text>
        </TouchableOpacity>
        <Text className="text-lg font-bold text-gray-900">New Group</Text>
        <TouchableOpacity 
          disabled={!groupName || selectedIds.size === 0}
          onPress={() => { /* Handle Create Logic */ }}
          className={(!groupName || selectedIds.size === 0) ? 'opacity-30' : ''}
        >
          <Text className="text-blue-500 text-lg font-bold">Create</Text>
        </TouchableOpacity>
      </View>

      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
        className="flex-1"
      >
        {/* Input Area */}
        <View className="flex-row items-center p-5">
          <TouchableOpacity className="w-16 h-16 rounded-2xl bg-gray-50 items-center justify-center border border-gray-200 border-dashed">
            <Text className="text-2xl">📸</Text>
          </TouchableOpacity>
          <TextInput
            className="flex-1 ml-4 text-xl font-semibold text-gray-900"
            placeholder="Group Name"
            value={groupName}
            onChangeText={setGroupName}
            placeholderTextColor="#9CA3AF"
          />
        </View>

        {/* Search Bar */}
        <View className="px-4 pb-3">
          <View className="bg-gray-100 rounded-xl px-3 flex-row items-center">
            <TextInput
              className="flex-1 p-3 text-base"
              placeholder="Search members..."
              value={search}
              onChangeText={setSearch}
              clearButtonMode="while-editing"
            />
          </View>
        </View>

        <View className="px-4 py-2 bg-gray-50 border-y border-gray-100">
          <Text className="text-[10px] font-bold text-gray-400 tracking-widest uppercase">
            Suggested Contacts • {selectedIds.size} Selected
          </Text>
        </View>

        {/* LegendList */}
        <LegendList
          data={filteredContacts}
          renderItem={renderContact}
          keyExtractor={(item) => item.id}
          estimatedItemSize={72}
          contentContainerStyle={{ paddingHorizontal: 16 }}
          drawDistance={500}
          ListEmptyComponent={<View className='mt-6 p-2 rounded-lg text-white items-center justify-center'>
            <Text className=' bg-red-100  p-6 rounded-full border-red-900 border-2'>No Contacts found</Text>
            </View>}
          keyboardShouldPersistTaps="handled"
        />
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

export default CreateGroup;