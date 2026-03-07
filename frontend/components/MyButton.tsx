import React from "react";
import { TouchableOpacity, Text, StyleSheet } from "react-native";

const MyButton = ({ title, onPress, style, textStyle, disabled }) => {
  return (
    <TouchableOpacity
      style={[
        styles.button,
        style,
        disabled && styles.disabled
      ]}
      onPress={onPress}
      activeOpacity={0.8}
      disabled={disabled}
      className="px-15 py-4 items-center justify-center mx-8"
      >
      <Text style={[styles.text, textStyle]}>
        {title}
      </Text>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  button: {
    backgroundColor: "#AD79DA",
    //paddingVertical: 18,
    //paddingHorizontal: 14,
    borderRadius: 8,
    justifyContent: "center",
  },
  text: {
    color: "#FFFFFF",
    fontSize: 16,
    fontWeight: "600",
  },
  disabled: {
    backgroundColor: "#A5B4FC",
  }
});

export default MyButton;