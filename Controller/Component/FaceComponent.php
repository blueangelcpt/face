<?php
App::uses('HttpSocket', 'Network/Http');
class FaceComponent extends Component {
	public $settings = array(
		'ApiKey' => '',
		'url' => 'https://southafricanorth.api.cognitive.microsoft.com/face/v1.0'
	);
	private $socket = '';
	private $tag = 'Face';

	public function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
		$this->settings = array_merge($this->settings, $settings);
		$this->socket = new HttpSocket(array(
			'ssl_verify_peer' => false,
			'ssl_verify_peer_name' => false,
			'ssl_allow_self_signed' => true,
			'ssl_verify_depth' => 0,
			'timeout' => 60
		));
	}

	public function detect($image_url) {
		$payload = json_encode(array(
			'url' => $image_url
		));
		$result = $this->socket->post(
			$this->settings['url'] . '/detect?recognitionModel=recognition_02&returnFaceId=true',
			$payload,
			array('header' => array(
				'Ocp-Apim-Subscription-Key' => $this->settings['ApiKey'],
				'Content-Type' => 'application/json',
			))
		);
		$this->log('Face detect API response: ' . $result, $this->tag);
		$result = json_decode($result->body, true);
		return $result[0]['faceId'];
	}

	public function verify($face_id_1, $face_id_2) {
		$payload = json_encode(array(
			'faceId1' => $face_id_1,
			'faceId2' => $face_id_2
		));
		$result = $this->socket->post(
			$this->settings['url'] . '/verify',
			$payload,
			array('header' => array(
				'Ocp-Apim-Subscription-Key' => $this->settings['ApiKey'],
				'Content-Type' => 'application/json',
			))
		);
		$this->log('Face verify API response: ' . $result, $this->tag);
		return json_decode($result->body, true);
	}
}