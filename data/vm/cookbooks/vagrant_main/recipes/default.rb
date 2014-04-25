include_recipe "apt"
include_recipe "google-dns"
include_recipe "htop"
include_recipe "networking_basic"
include_recipe "apache2"
include_recipe "apache2::mod_php5"
include_recipe "apache2::mod_rewrite"
include_recipe "apache2::mod_deflate"
include_recipe "apache2::mod_headers"
include_recipe "php"
include_recipe "git"
include_recipe "nodejs" # version 0.10.xx includes npm
include_recipe "zsh"
include_recipe "oh-my-zsh"
include_recipe "users"
include_recipe "vim"
include_recipe "web_build_tools"
include_recipe "supervisor"

# Initialize web app
web_app "default" do
    template "default.conf.erb"
    server_name "endpoint.spaceapi.net"
    server_aliases [node['fqdn'], "localhost"]
    docroot "/vagrant/public"
end
