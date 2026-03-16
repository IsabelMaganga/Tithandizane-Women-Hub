import React from "react";
import { TouchableOpacity, View, StyleSheet } from "react-native";
import { MaterialIcons,AntDesign } from '@expo/vector-icons';

// Accept any Expo vector icon component as a prop
const IconButton = ({ IconComponent,named, size = 24, color = "#000", onPress, style }) => {
  if (!IconComponent) return null; // don't render if no icon passed

  return (
    <TouchableOpacity onPress={onPress} style={[styles.button, style]}>
      <IconComponent name={named} size={size} color={color} />
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  button: {
    justifyContent: "center",
    alignItems: "center",
  },
});

export default IconButton;