import { Pressable,View,Text } from "react-native";
import { useTranslation } from "react-i18next";

const {t} = useTranslation();

export const MenuItem = ({ title, icon, bgColor, onPress, family: IconFamily }: any) => (
    
    <Pressable 
      onPress={onPress}
      className="w-[45%] aspect-square items-center justify-center mb-4 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700"
    >
      <View className={`${bgColor} p-4 rounded-2xl mb-2 shadow-inner`}>
        <IconFamily name={icon} size={28} color="white" />
      </View>
      <Text className="text-slate-800 dark:text-slate-100 font-semibold text-center px-2">
        {t(title)}
      </Text>
    </Pressable>
  );