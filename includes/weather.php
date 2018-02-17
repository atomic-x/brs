<?
/************************************************ 
/ Class: WeatherData 
/ Author: Beau Watson 
/ Version: 1.1 
/ Description: Uses the Yahoo Weather RSS API to get 
/  weather info for US cities by zip code 
/ Special Thanks: Navarr Barnier (tech.navarr.me) 
/************************************************/
class WeatherData  
{  
    private $url; //variable to store our url  
    private $cond; //variable to store our condition
    private $code;
    private $location;
    private $image;
    private $temp; //variable to store our temperature  
    private $humidity; //variable to store our humidity  
    private $units; //variable so we can set units f=farenheit, c=celsius  
    private $data; //array to hold our weather data from Yahoo  
    private $matches; //array to hold our regex matches  
    private $xml; //variable to hold the xml data from the RSS feed  
    private $item; //variable to hold the node we're looking at  
    
    //Constructor intializes properties of the class  
    //pass it a zip code, you can also define units  
    //defaults to farenheit, but pass in c for celsius  
    public function __construct( $zip, $units = "f")  
    {
        if($this->units != "c"){  
            $this->units = "f";  
        }
        
        //set the url given the things passed into the constructor  
        $this->url       = 'http://weather.yahooapis.com/forecastrss?p='.$zip.'&u='.$units;  
        //open the file  
        $this->xml = file_get_contents($this->url);  
        //reead it using SimpleXML  
        $this->xml = simplexml_load_string($this->xml);  
        //we grab the channel node using simpleXML  
        $this->item = $this->xml->channel[0]->item[0];  
        //here we set the namespace we're looking for using yahoo's spec  
        $yweather = $this->item->children("http://xml.weather.yahoo.com/ns/rss/1.0");
        //we have to redeclare a second namespace to use since yahoo's API sticks the  
        //  humidity data that we need in a different tag
        $yweather2 = $this->xml->channel->children("http://xml.weather.yahoo.com/ns/rss/1.0");
        $this->location = str_replace("Yahoo! Weather - ", "", $this->xml->channel->title);
        //we grab all of the attributes of the yweather tag for the conditions  
        $attr = $yweather->condition->attributes();
        //we grab the temp attribute and set the temperature property  
        $this->temp     = $attr["temp"];  
        //we grab the condition type and set the property  
        $this->cond     = $attr["text"];  
        $this->code     = $attr["code"];
        $this->image    = $this->xml->channel->image->url;
        //we use our attribute finder and our other namespace to get  
        // the atmosphere tag, where we get the humidity data  
        $attr = $yweather2->atmosphere->attributes();  
        //set the humidity property  
        $this->humidity  = $attr["humidity"];
    }
    
    //magic function for setting variables  
    public function __set($var, $val)  
    {  
        $this->$var = $val;  
    }  
    
    //magic function for returning variables  
    public function __get($var)  
    {  
        return $this->$var;  
    }
}
?>