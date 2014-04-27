
require_recipe "php"
require_recipe "nodejs"
require_recipe "web_build_tools"

execute "initialize_project" do
    cwd '/vagrant'
    command <<-EOH
        php composer.phar self-update
        php composer.phar update
        # execute's user option didn't work, so we simply allow bower getting executed by root
	bower --allow-root --config.interactive=false update
        npm install
        grunt build
        echo "You might create some local Zend2 configuration files"
    EOH
end
