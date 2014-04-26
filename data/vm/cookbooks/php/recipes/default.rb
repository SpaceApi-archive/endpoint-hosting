require_recipe "python-software-properties"

execute "sudo add-apt-repository ppa:ondrej/php5-oldstable" do
end

execute "sudo apt-get update" do
end

packages = [
  'php5',
  'php5-cli'
]

packages.each do |pkg|
  package pkg do
    action :install
  end
end

template "/etc/php5/apache2/php.ini" do
  source "php.ini.erb"
  owner "root"
  group "root"
  mode "0644"
end

template "/etc/php5/cli/php.ini" do
  source "php.ini.erb"
  owner "root"
  group "root"
  mode "0644"
end

service "apache2" do
  action :restart
end