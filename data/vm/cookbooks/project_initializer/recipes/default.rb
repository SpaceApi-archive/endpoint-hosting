
require_recipe "php"
require_recipe "nodejs"
require_recipe "web_build_tools"

execute "initialize_project" do
    user "vagrant"
    command <<-EOH
        cd /vagrant
        php composer.phar self-update
        php composer.phar update
        bower --config.interactive=false update
        npm install
        grunt build
        echo "You might create some local Zend2 configuration files"
    EOH
end
