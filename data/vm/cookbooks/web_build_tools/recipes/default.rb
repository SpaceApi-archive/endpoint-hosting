
require_recipe "nodejs"

execute "install_frontend_package_manager" do
    command "sudo npm install -g bower"
    not_if "which bower"
end

execute "install_web_build_tool" do
    command "sudo npm install -g grunt-cli"
    not_if "which grunt"
end

package "ruby-sass" do
  action :install
end