import { View, Text } from 'react-native'
import React from 'react'
import { Modal } from 'react-native-paper'

const ComfirmModal = () => {
  return (
    <View>
        <Modal visible={true} onDismiss={() => {}}>
          <Text>Confirm Modal</Text>
        </Modal>

    </View>
  )
}

export default ComfirmModal