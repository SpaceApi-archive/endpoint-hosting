require_recipe "python-software-properties"

execute "add_ppa_php5-oldstable" do
    command "sudo add-apt-repository ppa:ondrej/php5-oldstable && sudo apt-get update"
    not_if "grep -o ondrej/php5-oldstable /etc/apt/sources.list /etc/apt/sources.list.d/*"
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