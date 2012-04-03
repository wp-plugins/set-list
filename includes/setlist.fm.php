<?php

class slp_setlistfm {
    private $apiBase = 'http://api.setlist.fm/rest/0.1/';
    private $apiUrl = 'http://api.setlist.fm/rest/0.1/';  
  
	public function __construct() {}
    
    private function urlEncodeParams($params)
    {
        $postdata = '';
        if(!empty($params)){
            foreach($params as $key => $value)
            {
				//$postdata .= '&'.$key.'='.str_replace(' ','+',$value) ;
				$postdata .= '&'.$key.'='.urlencode($value);
            }
        }
        $postdata = '?'.substr($postdata,1, strlen($postdata)-1);

		return $postdata;
    }
    
    public function http($url, $params, $method) {
      
		//get data back as json	
		$url .= '.json';
		
		//add any params
		$url = $url.$this->urlEncodeParams($params);
		
		//PRINT $url;
			
		$json = @file_get_contents( $url,0,null,null);

		if($json==FALSE) { 
			
			//handle error here... 
			throw new setlistApiError('The setlist.fm API is currently down');
			
		} else {
		
			$json = str_replace('@','',$json);
			$r = json_decode( $json);
				
			if ($r == null) {
			
				throw new setlistApiError('setlist.fm Error: Setlist '.$json);
				die();
			
			} else {
			
						
				return $r;		
			
			}
		
		
		
		}
			
    }
    
    // http GET request
    public function get($endpoint, $params=array(), $method='GET'){
      
		return $this->http($this->apiUrl.$endpoint, $params, $method);
    }
    
    public function api_check() {
    
    	    
    	$params =  array(	'artistName' => 'Drake'
							,'date' => '01-03-2012'
							,'venueName' =>  'Sprint Center'
							
							);
							
		$url = 'search/setlists';
		
		try {
		
			$setlist = $this->get($url, $params);
			
			$code = 'connected';
			$check = "The setlist.fm API is up and running";
			
		}
		catch(Exception $e) {
					
			$code = 'disconnected';
			$check = $e->getMessage();
				
					
		}
		
		$api = array($code,$check);
      	return $api;
		
    
    }
	
	
	public function get_setlist($artistName, $date, $venueName)	{
	
		
		if ($venueName == '') { $venueName = null;}
		
		$params =  array(	'artistName' => $artistName
							,'date' => $date
							,'venueName' =>  $venueName
							
							);
							
		$url = 'search/setlists';
	
		$setlist = $this->get($url, $params);
		
		$set = $setlist->setlists->setlist;
		
		if (is_array($set) && $venueName == null) {
		 
			throw new setlistApiError('Multiple gigs by '.ucwords($artistName).' on '.$date.', please enter a venue name');
			die();
		
		} else {
		
			return $set;
		
		}
	
	}
	
	public function get_set_artist($set) {
	
		
		$artist = $set->artist;
		
		return $artist;
	
	}
	
	public function get_set_venue($set) {
	
			
		$venue = $set->venue;
				
		return $venue;
	
	}

	
	public function get_set_songs($set) {
	
		$set = $set->sets->set;
		
		if (is_array($set)) {
			$songs = $set[0]->song;
		
		
		} elseif ( is_object($set))
			
			$songs = $set->song;
			
			
		else {
		
			$songs = array();
			$custom = (object)array('name'=>$set->song);
			$songs[] = $custom;
			
		}
		
		foreach($songs as $song):
		
			//strip out quotes
			$song->name	= str_replace('"','',$song->name);		
												
		endforeach;
		
		return $songs;
	
	}
	
	public function get_set_encore($set) {
			
		$set =  $set->sets->set;
		if (is_array($set)) {
			$encore = $set[1]->song;
			
			if (!is_array($encore)) {
			
				$song = str_replace('ENCORE','',$encore->name);		
				$song = str_replace(':','',$song);	
				$song = str_replace('"','',$song);	
				
				$encore = array();
			
				$custom = (object)array('name'=>$song);
				$encore[] = $custom;

		
			}
	
			if (is_array($encore)) {
			
				foreach($encore as $song):
			
				//strip out quotes
				$song->name	= str_replace('ENCORE','',$song->name);		
													
				endforeach;
			
			}
			
			
		
		} else {
		
			$encore = null;
		}
		
		
		
		return $encore;
	
	}
}

class setlistApiError extends Exception {}

?>