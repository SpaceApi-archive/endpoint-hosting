
require_recipe "python-software-properties"

execute "sudo add-apt-repository ppa:chris-lea/node.js" do
end

execute "sudo apt-get update" do
end

package "nodejs" do
  action :install
end