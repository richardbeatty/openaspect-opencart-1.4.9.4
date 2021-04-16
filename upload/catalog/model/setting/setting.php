<?php 
class ModelSettingSetting extends Model {
	public function getSetting($group) {
		$data = array(); 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting ");
		
		foreach ($query->rows as $result) {
			$data[$result['key']] = $result['value'];
		}
				
		return $data;
	}
	
}
?>