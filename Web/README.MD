# Submodule for CHIIA-NLP

NLP_server is the web server of the whole project. 
This web server is based on Thinkphp frameworks, work with mongodb and deploys on apache2. And the PHP version should be above PHP 5.4.

## Directory Structure

<pre>
project                         application deployment directory        
   |- application               application directory   
   |    |- common               common module directory  
   |    |- index                module directory            
   |    |   |- controller       controller             
   |    |   |- model            model directory   
   |    |   |- view             view directory  
   |    |   |_ ...              more module directory  
   |    |  
   |    |- command.php          command tool configuration  
   |    |- common.php           common function file  
   |    |- config.php           public configuration  
   |    |- database.php         database configuration  
   |    |- tags.php             application action extension  
   |    |_ route.php            router configuration  
   |  
   |- extend                    extension of module   
   |- public                    WEB deployment directory (External access directory)  
   |    |- static               static resources directory (css, js, image)  
   |    |- index.php            application entrance  
   |    |- router.php           quick test file  
   |    |- .htaccess            apache2 rewrite  
   |- runtime                   application runtime directory  
   |- vendor                    Third-parties libraries( Composer )  
   |- thinkphp                  Framework system files  
   |- LICENSE.txt               LICENSE file  
</pre>