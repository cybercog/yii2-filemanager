<?php

namespace nitm\filemanager;

use yii\helpers\ArrayHelper;
/**
 * Module class.
 */
class Module extends \yii\base\Module
{
    
    public $controllerNamespace = 'nitm\filemanager\controllers';
    
    public $thumbnails = [[100,100]];
	
	public $directorySeparator = DIRECTORY_SEPARATOR;
	
	/**
	 * This is used primarily for local permissions
	 * Permissions in the format:
	 *	[
	 		'type' => [
				'mode' => 
				'group' =>
				'owner' =>
			]
	 *	]
	 */
	public $permissions = [];
    
	/**
	 * The custom path map for different file types
	 */
    public $pathMap = [
		'image' => '@media/images/',
		'audio' => '@media/audio/',
		'video' => '@media/videos/',
		'text' => '@media/documents/',
		'application' => '@media/applications/',
		'unknown' => '@media/unknown/'
	];
	
	public $engine = 'local';
    
    public $thumbPath = 'thumb/';
    
    public $url = '/';
    
	/**
	 * The AmazonAWS configuration
	 */
    public $aws = [
        'enable' => false,
        'key' => ' => ',
        'secret' => ' => ',
    	'bucket' => ' => ',
    ];
	
	public $allowedTypes = [
	];
	
	private $_storageEngines = [
		'local' => 'Local',
		'aws' => 'AmazonAWS',
	];
    
    public function init()
    {
        parent::init();
		
		$this->permissions = array_merge($this->defaultPermissions(), $this->permissions);

        // custom initialization code goes here
		
		/**
		 * Aliases for nitm\widgets module
		 */
		\Yii::setAlias('nitm/filemanager', dirname(__DIR__)."/yii2-filemanager");
    }
	
	public function getPath($for=null)
	{
		return ArrayHelper::getValue($this->pathMap, $for, $this->pathMap['unknown']);
	}
	
	public function getType($for=null) 
	{
		return ArrayHelper::getValue($this->getTypeMap(), $for, $this->typeMap);
	}
	
	public function getExtension($for=null) 
	{
		return ArrayHelper::getValue(array_flip($this->getExtensionMap()), $for, null);
	}
	
	public function getBaseType($extension)
	{
		return ArrayHelper::getValue($this->getBaseTypeMap(), $extension, 'unknown');
	}
	
	public function getIsAllowed($extension)
	{
		return in_array($extension, $this->getAllowedTypes());
	}
	
	public function getAllowedTypes()
	{
		return (count($this->allowedTypes) == 0) ? array_keys($this->getBaseTypeMap()) : array_intersect(array_keys($this->getBaseTypeMap()), $this->allowedTypes);
	}
	
	public function getEngine($engine=null)
	{
		$engine = is_null($engine) ? $this->engine : $engine;
		if(isset($this->_storageEngines[$engine]))
			return $this->_storageEngines[$engine];
		else
			throw new \yii\base\Exception("Engine: $engine is not supported");
	}
	
	public function getEngineClass($engine=null)
	{
		$engine = is_null($engine) ? $this->engine : $engine;
		
		$class = "\\nitm\\filemanager\helpers\storage\\".$this->getEngine($engine);
		
		switch(class_exists($class))
		{
			case true:
			return $class;
			break;
		
			default:
			throw new \yii\base\Exception("There is no engine for [".$engine."] available");
			break;
		}
	}
	
	public function getPermission($types=null, $for=null)
	{
		$types = count($_types = array_map('trim', explode('|', $types))) == 0 ? array_keys($this->permissions) : $_types;
		$for = count($_for = array_map('trim', explode('|', $for))) == 0 ? null : $_for;
		
		$ret_val = [];
		foreach((array)$types as $idx=>$t)
		{
			if(is_array($for))
				$ret_val[$t] = ArrayHelper::getValue(ArrayHelper::getValue($this->permissions, $t, []), $for[$idx], null);
			else
				$ret_val[$t] = ArrayHelper::getValue($this->permissions, $t, []);
		}
		return count($ret_val) == 1 ? array_pop($ret_val) : $ret_val;
	}
	
	public function getWidget($type, $options=[])
	{
		$widgetClass = __NAMESPACE__.'\widgets\\'.ucfirst(strtolower($type));
		if(class_exists($widgetClass))
			return new $widgetClass($options);
		else
			return ' => ';
	}
	
	private function defaultPermissions() 
	{
		return [
			'directory' => [
				'mode' => '0770',
				'owner' => 'nobody',
				'group' => 'nogroup'
			],
			'file' => [
				'mode' => '0770',
				'owner' => 'nobody',
				'group' => 'nogroup'
			],
		];
	}
	
	private function getExtensionMap()
	{
		return [
			'pdf' => 'application/pdf',
			'zip' => 'application/zip',
			'pcap' => 'application/vnd.tcpdump.pcap',
			'cap' => 'application/vnd.tcpdump.pcap',
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'css' => 'text/css',
			'html' => 'text/html',
			'js' => 'text/javascript',
			'txt' => 'text/plain',
			'xml' => 'text/xml',
		];
	}

	private function getTypeMap()
	{
		return array_flip($this->getExtensionMap());
	}
	
	private function getBaseTypeMap()
	{
		return [
			'txt' => 'text',
			'htm' => 'text',
			'html' => 'text',
			'php' => 'text',
			'css' => 'text',
			'js' => 'text',
			'json' => 'text',
			'xml' => 'text',
			'pdf' => 'text',
			'pcap' => 'text',
			'cap' => 'text',
	
			// ms office
			'doc' => 'text',
			'rtf' => 'text',
			'xls' => 'text',
			'ppt' => 'text',
	
			// open office
			'odt' => 'text',
			'ods' => 'text',
	
			// images
			'png' => 'image',
			'jpe' => 'image',
			'jpeg' => 'image',
			'jpg' => 'image',
			'gif' => 'image',
			'bmp' => 'image',
			'ico' => 'image',
			'tiff' => 'image',
			'tif' => 'image',
			'svg' => 'image',
			'svgz' => 'image',
	
			// adobe
			'psd' => 'image',
			'ai' => 'image',
			'eps' => 'image',
			'ps' => 'image',
	
			// archives
			'swf' => 'application',
			'zip' => 'application',
			'rar' => 'application',
			'exe' => 'application',
			'msi' => 'application',
			'cab' => 'application',
	
			// audio/video
			'mp3' => 'audio',
			'qt' => 'video',
			'mov' => 'video',
			'flv' => 'video',
		];
	}
}

