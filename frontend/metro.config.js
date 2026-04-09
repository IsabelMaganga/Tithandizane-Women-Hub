// Learn more https://docs.expo.io/guides/customizing-metro
const { getDefaultConfig } = require('expo/metro-config');
const { withNativeWind } = require('nativewind/metro');

/** @type {import('expo/metro-config').MetroConfig} */
const config = getDefaultConfig(__dirname);

// Add network configuration for React Native
config.resolver.assetExts.push('json');
config.resolver.sourceExts.push('json');
config.transformer.minifierConfig = false;

module.exports = withNativeWind(config, { input: './global.css' })
