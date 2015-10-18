
/************************************************
Writen by GengHongChun

入口参数为d_id, t_id, p_id, 根据用户输入修改一文档并发布之

************************************************/
#include "gsql.h"
#include "cgi.h"
#include "myperl.h"
#include "publish.h"
#include "priv.h"


int main(int argc, char **argv, char **env)
{
	
	MYSQL mysql, mysql1;
	MYSQL_RES *res, *res1;
	char **row, **row1;
	char *sqlstr, *sqlstr1;
	int  alloc_size;
	char *p_id, *t_id, *d_id, *t_name, *t_cname;
	CGI *pcgi;
	
	char *f_name, *cname, *type, *arithmetic;
	int polynum;
	char *buff, nav_buf[800];
	char *ghc;
	int  i, bi, len;
	int u_type;
	
	
	pcgi = getCGI("/tmp");

	d_id = cgiParam(pcgi, "d_id");
	t_id = cgiParam(pcgi, "t_id");
	p_id = cgiParam(pcgi, "p_id");

	if(!p_id || !t_id || !d_id)
	{
		printf("Content-type: text/html\n\n");
		printf("Invalid parameters!!");
		exit(1);
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


	printf("Content-type: text/html\n\n");
	

	if(u_type < 100)
	{
		sprintf(nav_buf, "/<a href=\"proj.cgi\">发布中心</a>/<a href=\"temp.cgi?p_id=%s\">%s</a>/%s(<a href=\"docu.cgi?t_id=%s&p_id=%s\">文档</a>)(<a href=\"this_temp.cgi?t_id=%s&p_id=%s\">模板</a>)/修改文档(%s)", p_id, proj_cname, PM[0].t_cname, t_id, p_id, t_id, p_id, d_id);
	}
	else
	{
		sprintf(nav_buf, "/<a href=\"/cgi/projlist.php\">网站管理中心</a> &gt; <a href=\"/cgi/templist.php?p_id=%s\">%s</a> &gt; %s(<a href=\"/cgi/doclist.php?t_id=%s&p_id=%s\">文档</a>) (<a href=\"/cgi/temp_edit.php?t_id=%s&p_id=%s\">模板</a>) (<a href=\"/cgi/tempdeflist.php?t_id=%s&p_id=%s\">模板域</a>) &gt; 修改文档(<a href=/cgi/doc_edit.php?p_id=%s&t_id=%s&d_id=%s>%s</a>)", p_id, proj_cname, PM[0].t_cname, t_id, p_id, t_id, p_id, t_id, p_id, p_id, t_id, d_id, d_id);
	}
	
	print_html("修改文档", nav_buf);
	fflush(stdout);



	alloc_size=2000;
	
	for(i=0; i<=pcgi->num; i++)
	{
		
		if(pcgi->inputs[i].name==NULL || pcgi->inputs[i].val==NULL) continue;
		//     printf("%d------%s=======----------%s======<br>\n", i, pcgi->inputs[i].name, pcgi->inputs[i].val);
		//replace(&(pcgi->inputs[i].val), "\\", "\\\\");
		//replace(&(pcgi->inputs[i].val), "'", "\\'");
		alloc_size+=strlen(pcgi->inputs[i].name)+strlen(pcgi->inputs[i].val);
	}
	
	
	
	sqlstr=(char *)malloc(alloc_size*2+800);
	sprintf(sqlstr, "update %s set savedatetime=now(), mu_id=%s,", PM[0].t_name,  ck_u_id);


	bi = strlen(sqlstr);
	
	for(i=0; i<=pcgi->num; i++)
	{
		char *p, *this_name, *this_value;
		char radio_field[200], *radio_value;

		if(pcgi->inputs[i].name==NULL || pcgi->inputs[i].val==NULL) continue;
		
		this_name=pcgi->inputs[i].name;
		this_value=pcgi->inputs[i].val;
		
		p=strstr(this_name, pre_field);
		if(p!=this_name) continue;

		sprintf(radio_field, "radio_%s", this_name);
		radio_value = cgiParam(pcgi, radio_field);
		if(radio_value)
		{
			if(!strcmp(radio_value, "old"))
			{
				continue;
			}
			else
			{
			/*
				char command[800];
				char urlbase[800];
				char *p;

				strncpy(urlbase, url);
				for(p=urlbase; *p; p++);
				while(p>urlbase && *p != '/') p--;
				*p = '\0';
				
				sprintf("mv /tmp/%s %s/%s", this_value, urlbase, this_value);
				system(command);
			*/
			}
		}

		p+=strlen(pre_field);
		bi += sprintf(sqlstr + bi, " %s='%s',", p, gsql_esc(this_value));
	}

	
	for(i=0; PM[i].pm_id>0; i++)
	{
		char cgi_name[600],  *doc_url;
		sprintf(cgi_name, "doc_url_%d", PM[i].pm_id);
		doc_url=cgiParam(pcgi, cgi_name);
		sprintf(sqlstr+strlen(sqlstr), " url_%d='%s',", PM[i].pm_id, doc_url);
	}
	
	
	sqlstr[strlen(sqlstr)-1]='\0';
	sprintf(sqlstr+strlen(sqlstr), " where d_id=%s", d_id);
//	printf("更新数据库....%s<br>\n", sqlstr);
	printf("更新数据库....<br>\n");
	

	if( dbexecute(proj_mysql, sqlstr) < 0 )
	{
		showMsg("系统忙", "提示", 0, "[%s]", error_message);
		return 0;
	}
	printf("更新数据库完成!<br>\n");
	
	perl_start();
	perl_connect_db(db_host, db_name, db_user, db_passwd, db_port, db_sock);
	if(publishOneDoc(atoi(p_id), atoi(t_id), atoi(d_id)) <0)
	{
		printf("发布文档失败%s<br>\n", error_message);
	}
	perl_end();
	return 0;
	
}


