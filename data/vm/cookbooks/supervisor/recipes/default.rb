
package "supervisor" do
  action :install
end

template "/etc/supervisor/conf.d/grunt_watch.conf" do
  backup false
  source "grunt_watch.conf.erb"
  owner "root"
  group "root"
  mode 00644
end

service "supervisor" do
  action :restart
end