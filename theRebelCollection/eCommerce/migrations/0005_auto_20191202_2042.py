# Generated by Django 2.2.7 on 2019-12-02 20:42

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0004_lists'),
    ]

    operations = [
        migrations.RenameField(
            model_name='lists',
            old_name='type',
            new_name='location',
        ),
    ]
