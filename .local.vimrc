" ノラプロジェクト用のVIMRC
"
" vim-tags
au BufNewFile,BufRead *.php let g:vim_tags_project_tags_command = "ctags -R -a --languages=PHP `pwd`"
