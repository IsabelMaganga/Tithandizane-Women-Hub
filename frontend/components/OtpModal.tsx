import { View, Text } from 'react-native'
import React from 'react'
import { Modal, TextInput } from 'react-native-paper'

const OtpModal = (isVisible) => {
  return (
    <Modal visible={isVisible} onDismiss={()=>{}}>
        <View>
            <Text>Enter Your OTP</Text>
            <View className='flex-row '>
                <TextInput placeholder='0'/>
                <TextInput placeholder='0'/>
                <TextInput placeholder='0'/>
                <TextInput placeholder='0'/>
            </View>
        </View>
    </Modal>
  )
}

export default OtpModal