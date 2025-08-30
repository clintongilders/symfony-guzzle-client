<?php

namespace App\Controller;

use Eprofos\UserAgentAnalyzerBundle\Service\UserAgentAnalyzer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAgentController extends AbstractController
{
    #[Route('/user-agent', name: 'app_user_agent')]
    public function userAgent(UserAgentAnalyzer $analyzer)
    {
        $result = $analyzer->analyzeCurrentRequest();
        
        // Access the results
        $osName = $result->getOsName();           // e.g., "Windows"
        $osVersion = $result->getOsVersion();     // e.g., 10.0
        $browserName = $result->getBrowserName(); // e.g., "Chrome"
        $deviceType = $result->getDeviceType();   // e.g., "desktop"
        
        // Check device type
        $isMobile = $result->isMobile();    // true if device is mobile
        $isDesktop = $result->isDesktop();  // true if device is desktop
        $isTablet = $result->isTablet();    // true if device is tablet
        
        // Get all information as array
        $allInfo = $result->toArray();

        return new Response(
            
            phpinfo(INFO_ALL)
        );
    }
}