<?php
class IndexController extends Controller_Abstract
{
	private $domain = null;
	private $protocol = null;
	
	public function getDomain() {
		return $this->domain;
	}
	
	public function setDomain($domain) {
		$this->domain = $domain;
	}
	
	public function getProtocol() {
		return $this->protocol;
	}
	
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}
	
	public function indexAction()
	{
	}
	
	public function parseAction()
	{
		set_time_limit(0);
    	ignore_user_abort(1);
		ini_set('xdebug.max_nesting_level', 100000);
		
		Links::getInstance()->deleteAll();
		
		$this->_params['domain-name'] = trim(trim($this->_params['domain-name']), '/');
		
		if (strpos($this->_params['domain-name'], 'https://') === 0) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}
		
		$this->setProtocol($protocol);
		
		$domain = str_replace($protocol, '', $this->_params['domain-name']);
		
		$this->setDomain($domain);
		
		$this->getPageLinks($protocol . $domain);
		
		exit;
	}
	
	private function getPageLinks($url) {
		
		$contents = @file_get_contents($url);
		
		if( FALSE !== $contents ){ 
			preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $contents, $matches);
			$urls = $matches[1];
			
			$protocol = $this->getProtocol();
			$domain = $this->getDomain();
			
			$array_links = array($url);
			
			for ($i = 0; $i < count($urls); $i++) {
				$str = trim(trim($urls[$i]), '/');
				
				if ((strpos($str, 'mailto:') === 0) || 
					(strpos($str, 'skype:')  === 0) || 
					(strpos(strtolower($str), '.jpeg'))  || 
					(strpos(strtolower($str), '.jpg')) || 
					(strpos(strtolower($str), '.png')) ||
					(strpos(strtolower($str), '.gif')) || 
					(strpos(strtolower($str), '.pdf')) ||
					(strpos(strtolower($str), '.doc')) ||
					(strpos(strtolower($str), '.odt')) ||
					(strpos(strtolower($str), '.docx')) ||
					(strpos($str, '?')) || 
					(strpos($str, '&')) || 
					(strpos($str, '$'))) {
					continue;
				}
				
				if ($str) {
					if (strpos($str, $protocol) === 0) {
						
						if(strpos($str, $protocol . $domain) === 0) {
							if (!in_array($str, $array_links)) {
								$array_links[] = $str;
							}
						}
					} else {
						if (!in_array($protocol . $domain . '/' . $str, $array_links)) {
							$array_links[] = $protocol . $domain . '/' . $str;
						}
					}
				}
			}
			
			$this->saveArrayLinks($array_links, $url, $flag);
			
	    } else {
	    	
		    $link = Links::getInstance()->getLinkByName($url);
		    
			if ($link['id']) {
				Links::getInstance()->deleteByID($link['id']);
			}
			
			$not_parse_link = Links::getInstance()->getNotParseLink();
			
			if ($not_parse_link['link']) {
				$this->getPageLinks($not_parse_link['link']);
			} else {
				$this->calculateFrequency();
			}
	    }
	}
	
	private function saveArrayLinks($array_links = array(), $url) {
		
		if (count($array_links)) {
			$links_db = Links::getInstance()->getLinksName();
			
			foreach ($array_links as $value) {
				$parse = ($value == $url) ? 1 : 0;
				
				if (!in_array($value, $links_db)) {
					if ($value) {
						if ($parse) {
							Links::getInstance()->insert(array('link' => $value, 'count' => 1, 'parse' => 1));
						} else {
							Links::getInstance()->insert(array('link' => $value, 'count' => 1));
						}
					}
				} else {
					$link = Links::getInstance()->getLinkByName($value);
					if ($link['id']) {
						if ($parse) {
							Links::getInstance()->update($link['id'], array('count' => $link['count'] + 1, 'parse' => 1));
						} else {
							Links::getInstance()->update($link['id'], array('count' => $link['count'] + 1));
						}
					}
				}
			}
		}
		
		$not_parse_link = Links::getInstance()->getNotParseLink();
		
		if ($not_parse_link['link']) {
			$this->getPageLinks($not_parse_link['link']);
		} else {
			$this->calculateFrequency();
		}
	}
	
	private function calculateFrequency() {
		$links_db = Links::getInstance()->getAll();
		
		foreach ($links_db as $value) {
			$arr[$value['count']][] = $value['id'];
		}
		
		ksort($arr);
		
		$count = count($arr);
		$prec = (10 / $count)/10;
		
		foreach ($arr as $value) {
			$i++;
			if (count($value)) {
				foreach ($value as $val) {
					if ($val) {
						Links::getInstance()->update($val, array('freq' => $prec * $i ));
					}
				}
			}
		}
		
		$this->createXmlFile();
	}
	
	private function createXmlFile() {
		
		$links_db = Links::getInstance()->getAllSortByFreq('desc');
		
		$dom = new domDocument("1.0", "utf-8");
		$root = $dom->createElement("sitemap");

		$dom->appendChild($root);
		
		foreach ($links_db as $value) {
			if ($value) {
				$link = $dom->createElement("element");
				foreach ($value as $key => $val) {
					if ($key == 'id') {
		    			$link->setAttribute("id", $val);
					} else {
						$elem = $dom->createElement($key, $val);
						$link->appendChild($elem);
					}
				}
				$root->appendChild($link);
			}
		}
		
		$dom->save("sitemap.xml");
		
		$this->sendEMail();
	}
	
	private function sendEMail() {
		
		$mail = new Zend_Mail();
		$mail->setType(Zend_Mime::MULTIPART_RELATED);
		$mail->setBodyHtml('<p>Attachment</p>');
		$mail->setFrom('mail@mail.com', 'Example user');
		$mail->addTo($this->_params['email'], 'Username');
		$mail->setSubject('Sending sitemap xml file.');
		
		$fileContents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml');

		$file = $mail->createAttachment($fileContents);
		$file->filename = 'sitemap.xml';
		$mail->send();
	}
}
