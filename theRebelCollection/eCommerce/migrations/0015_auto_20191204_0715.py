# Generated by Django 2.2.7 on 2019-12-04 07:15

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0014_auto_20191204_0703'),
    ]

    operations = [
        migrations.CreateModel(
            name='Reviews',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('reviewer', models.CharField(max_length=200)),
                ('product', models.CharField(max_length=200)),
                ('number', models.PositiveSmallIntegerField(blank='true', choices=[(1, 1), (2, 2), (3, 3), (4, 4), (5, 5)], null='true')),
                ('text', models.TextField(blank='true', null='true')),
            ],
        ),
        migrations.DeleteModel(
            name='Review',
        ),
    ]