import { useColorScheme } from "nativewind";

export function useThemeToggle() {
  const { colorScheme, setColorScheme } = useColorScheme();

  const toggleTheme = () => {
    setColorScheme(colorScheme === "dark" ? "light" : "dark");
  };

  return { colorScheme, toggleTheme };
}