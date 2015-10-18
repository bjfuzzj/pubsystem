
/************************************************
Writen by GengHongChun

入口参数为t_id, p_id，根据用户输入新建一文档并发布

************************************************/
#include "gsql.h"
#include "cgi.h"
#include "mysys.h"
#include "myperl.h"
#include "publish.h"
#include "priv.h"

int main(int argc, char **argv, char **env)
{
	
	MYSQL mysql;
	MYSQL_RES *res, *res1;
	char **row, **row1;
	char *sqlstr, *sqlstr1;
	int  alloc_size;
	char *p_id, *t_id, *t_name, *t_cname;
	
	unsigned long d_id;
	CGI *pcgi;
	
	
	char *f_name, *cname, *type, *arithmetic;
	int polynum;
	
	char  nav_buf[800];
	
	char *buff;
	int  i, len;
	int  u_type;
	

	pcgi = getCGI("/tmp");

	t_id = cgiParam(pcgi, "t_id");
	p_id = cgiParam(pcgi, "p_id");

	if( !p_id || !t_id )
	{
		printf("Content-type: text/html\n\n");
		printf("Invalid parameters!!");
		return -1;

	}
	
	
	if(get_cookie(PUB_COOKIE_NAME) < 0)
	{
		printf("Content-type: text/html\n\n");
		showMsg(error_message, "提示", 0, "");
		return 0;
	}

	u_type = atoi(ck_u_type);
	
	get_proj_flag = atoi(t_id);
	if(connectProjDB(atoi(p_id))<0)
	{
		printf("Content-type: text/html\n\n");
		showMsg("系统忙", "提示", 0, error_message);
		return 0;
	}
	
	
	if( check_priv(atoi(p_id), atoi(t_id), 0) < 0 )
	{
		printf("Content-type: text/html\n\n");
		showMsg("对不起，你没有操作权限", "提示", 0, error_message);
		return 0;
	}
	
	get_current_time();
	
	if(reset_cookie(PUB_COOKIE_NAME) < 0)
	{
		printf("Content-type: text/html\n\n");
		showMsg(error_message, "提示", 0, "");
		return 0;
	}
	
	
	
	t_name = PM[0].t_name;
	t_cname = PM[0].t_cname;

	printf("Content-type: text/html\n\n");


	if(u_type < 100)
	{
		sprintf(nav_buf, "/<a href=\"proj.cgi\">发布中心</a>/<a href=\"temp.cgi?p_id=%s\">%s</a>/%s(<a href=\"docu.cgi?t_id=%s&p_id=%s\">文档</a>)(<a href=\"this_temp.cgi?t_id=%s&p_id=%s\">模板</a>)/添加文档", p_id, proj_cname, t_cname, t_id, p_id, t_id, p_id);
	}
	else
	{
		sprintf(nav_buf, "/<a href=\"/cgi/projlist.php\">网站管理中心</a> &gt; <a href=\"/cgi/templist.php?p_id=%s\">%s</a> &gt; %s(<a href=\"/cgi/doclist.php?t_id=%s&p_id=%s\">文档</a>) (<a href=\"/cgi/temp_edit.php?t_id=%s&p_id=%s\">模板</a>) (<a href=\"/cgi/tempdeflist.php?t_id=%s&p_id=%s\">模板域</a>) &gt; 添加文档", p_id, proj_cname, PM[0].t_cname, t_id, p_id, t_id, p_id, t_id, p_id );
	}
	
	print_html("添加文档", nav_buf);

	printf("添加数据库记录....");


	sqlstr=(char *)malloc(800);
	
	sprintf(sqlstr, "insert into %s (cu_id, mu_id, createdatetime, savedatetime, published) values(%s, %s, now(), now(), 'n')", t_name, ck_u_id, ck_u_id);
	
	if( dbexecute(proj_mysql, sqlstr) < 0 )
	{
		printf("Content-type: text/html\n\n");
		showMsg("系统忙", "提示", 0, error_message);
		return 0;
	}
	d_id=mysql_insert_id(proj_mysql);
	
	
	alloc_size=2000;
	
	for(i=0; i<=pcgi->num; i++)
	{
		if(!pcgi->inputs[i].name || !pcgi->inputs[i].val) continue;
		//        printf("%d------%s=======----------%s======<br>\n", i, pcgi->inputs[i].name, pcgi->inputs[i].val);
		//replace(&(pcgi->inputs[i].val), "\\", "\\\\");
		//replace(&(pcgi->inputs[i].val), "'", "\\'");
		alloc_size+=strlen(pcgi->inputs[i].name)+strlen(pcgi->inputs[i].val);
	}
	
	
	
	sqlstr=(char *)realloc(sqlstr, alloc_size*2+800);
	
	sprintf(sqlstr, "update %s set", t_name);
	for(i=0; PM[i].pm_id>0; i++)
	{
		char cgi_name[600], *url_radio,  *default_url, *outer_url;
		
		sprintf(cgi_name, "urlradio_%d", PM[i].pm_id);
		url_radio=cgiParam(pcgi, cgi_name);
		
		sprintf(cgi_name, "outer_url_%d", PM[i].pm_id);
		outer_url=cgiParam(pcgi, cgi_name);
		
		sprintf(cgi_name, "default_url_%d", PM[i].pm_id);
		default_url=cgiParam(pcgi, cgi_name);
		
		if(!strcmp(url_radio, "default"))
		sprintf(sqlstr+strlen(sqlstr), " url_%d='%s',", PM[i].pm_id, default_url);
		else
		sprintf(sqlstr+strlen(sqlstr), " url_%d='%s',", PM[i].pm_id, outer_url);
		
	}
	
	
	
	for(i=0; i<=pcgi->num; i++)
	{
		char *p, *this_name, *this_value;
		char mark[300];
		
		
		if(!pcgi->inputs[i].name || !pcgi->inputs[i].val) continue;
		
		sprintf(mark, "%spoly_", pre_field);
		
		this_name=pcgi->inputs[i].name;
		this_value=pcgi->inputs[i].val;
		
		p=strstr(this_name, mark);
		if(p==this_name)
		{
			int k;
			p+=strlen(mark);
			for(k=0; PM[k].pm_id>0; k++)
			sprintf(sqlstr+strlen(sqlstr), " %s_%d='%s',", p, PM[k].pm_id,  gsql_esc(this_value));
		}
		else
		{
			p=strstr(this_name, pre_field);
			if(p!=this_name) continue;
			p+=strlen(pre_field);
			sprintf(sqlstr+strlen(sqlstr), " %s='%s',", p, gsql_esc(this_value));
		}
	}
	sqlstr[strlen(sqlstr)-1]='\0';
	sprintf(sqlstr+strlen(sqlstr), " where d_id=%ld", d_id);
	if(dbexecute(proj_mysql, sqlstr) < 0)
	{
		printf("Content-type: text/html\n\n");
		showMsg("系统忙", "提示", 0, error_message);
		return 0;
	}
	printf("完成!<br>\n");


	perl_start();
	perl_connect_db(db_host, db_name, db_user, db_passwd, db_port, db_sock);
	publishOneDoc(atoi(p_id), atoi(t_id), d_id);
	perl_end();

	return 0;
	
}





