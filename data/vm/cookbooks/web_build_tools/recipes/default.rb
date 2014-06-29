
require_recipe "nodejs"

execute "install_frontend_package_manager" do
    command "sudo npm install -g bower"
    not_if "which bower"
end

execute "install_web_build_tool" do
    command "sudo npm install -g grunt-cli"
    not_if "which grunt"
end

# we need a newer sass
#package "ruby-sass" do
#  action :install
#end

package "rubygems" do
  action :install
end

execute "rubygems-sass" do
    command "sudo gem install sass"
    not_if "which sass"
end
