import { Tabs } from "expo-router";
import { AntDesign, MaterialCommunityIcons } from "@expo/vector-icons";

export default function TabsLayout() {
  return (
    <Tabs screenOptions={{ headerShown: false, tabBarActiveTintColor: "#8A4FFF",tabBarStyle: {
          elevation: 0,
          shadowOpacity: 0,
          shadowOffset: { width: 0, height: 0 },
          shadowRadius: 0,
          borderTopWidth: 0,
          backgroundColor: '#fff',
        }, }}>

      <Tabs.Screen
        name="index"
        options={{
          title: "Home",
          tabBarIcon: ({ color, size,focused }) => (
             focused? (<MaterialCommunityIcons name="home" size={size} color={color} />):
            (<AntDesign name="home" size={size} color={color} />)
          )
        }}
      />

      <Tabs.Screen
        name="inbox"
        options={{
          title: "Chats",
          tabBarIcon: ({ color, size }) => (
            <AntDesign name="inbox" size={size} color={color} />
          ), headerShown: false,headerStyle: { elevation: 0, shadowOpacity: 0, shadowOffset: { width: 0, height: 0 }, shadowRadius: 0, borderBottomWidth: 0 },
        }}
      />
      <Tabs.Screen
        name="articles"
        options={{
          title: "Articles",
          tabBarIcon: ({ color, size }) => (
            <AntDesign name="file-text" size={size} color={color} />
          ), headerShown: false,headerStyle: { elevation: 0, shadowOpacity: 0, shadowOffset: { width: 0, height: 0 }, shadowRadius: 0, borderBottomWidth: 0 },
        }}
      />


    </Tabs>
  );
}